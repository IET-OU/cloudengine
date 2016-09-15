<?php
/**
 * Model file for blog-related functions
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Blog
 */
class Blog_model extends Model {

    function __construct() {
        parent::Model();
    }

    /**
     * Get all the blog posts
     *
     * @param integer $num Limit to the number of blog posts to get
     * @param integer $offset The offset from which to start getting the posts
     * @return array Array of blog posts
     */
    function get_blog_posts($num = 100, $offset = 0) {
        $num = (int) $num; // Make sure $num is an integer
        $query = $this->db->query("SELECT n.post_id, n.title, n.body,
                  n.created AS created, n.user_id AS user_id,
                  COUNT(nc.comment_id) AS total_comments,
                  up.fullname, p.picture
                  FROM blog_post n
                  INNER JOIN user_profile up ON n.user_id = up.id
                  LEFT OUTER JOIN blog_comment nc ON nc.post_id = n.post_id
                  LEFT OUTER JOIN user_picture p ON n.user_id = p.user_id
                  GROUP BY n.post_id ORDER BY created DESC LIMIT $num");

        return $query->result();
    }

    /**
     * Get a specific blog post
     *
     * @param integer $post_id The ID of the blog post
     * @return object The details of the blog post
     */
    function get_blog_post($post_id) {
        $post = false;
        $this->db->join('user_picture', 'blog_post.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = blog_post.user_id');
        $this->db->where('post_id', $post_id);
        $query = $this->db->get('blog_post');

        if ($query->num_rows() !=  0 ) {
            $post = $query->row();
        }
        return $post;
    }

    /**
     * Insert a blog post
     *
     * @param $post The blog post details
     * @return integer $post_id The ID of the new blog post
     */
    function insert_blog_post($post) {
        $post->created = time();
        $this->db->insert('blog_post', $post);
        $post_id =  $this->db->insert_id();
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('news', $post_id, 'news', $post->user_id);
        return $post_id;
    }

    /**
     * Update an existing blog post
     *
     * @param object $post The new blog post details
     */
    function update_blog_post($post) {
        $post_id = $post->post_id;
        $this->db->update('blog_post', $post, array('post_id'=>$post_id));
    }

    /**
     * Delete and existing blog post
     *
     * @param integer $post_id id of the blog post to delete
     */
    function delete_blog_post($post_id) {
        $this->db->delete('news', array('post_id' => $post_id));
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->delete_events('blog_post', $post_id);
    }

    /**
     * Get the comments on a blog post
     *
     * @param integer $post_id
     * @return array Array of comments
     */
    function get_comments($post_id) {
        $this->db->from('blog_comment');
        $this->db->where('blog_comment.post_id', $post_id);
        $this->db->where('blog_comment.moderate', 0);
        $this->db->where('user.banned', 0);
        $this->db->order_by('timestamp', 'asc');
        $this->db->join('user_profile', 'user_profile.id = blog_comment.user_id');
        $this->db->join('user', 'user_profile.id = user.id');
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Add a comment to a blog post
     *
     * @param integer $post_id The ID of the blog post
     * @param integer $user_id The ID of the user adding the comment
     * @param string $body The comment body (an HTML string)
     * @param string $moderate TRUE if the comment needs moderation, FALSE otherwise
     * @return integer The ID of the new comment
     */
    function insert_comment($post_id, $user_id, $body, $moderate) {
        $comment->post_id  = $post_id;
        $comment->body     = $body;
        $comment->user_id  = $user_id;
        if (!$moderate) {
            $moderate = 0;
        }
        $comment->moderate = $moderate;
        $comment->timestamp = time();

        $this->db->insert('blog_comment', $comment);
        $comment_id = $this->db->insert_id();

        // If comment not flagged for moderate, then approve it
        if (!$comment->moderate) {
            $this->approve_comment($comment_id);
        }
        return $comment_id;
    }

    /**
     * Get the comments that need to be moderated
     *
     * @return array Array of comments
     */
    function get_comments_for_moderation() {
        $this->db->where('blog_comment.moderate', 1);
        $this->db->where('user.banned', 0);
        $this->db->join('user', 'user.id = blog_comment.user_id');
        $this->db->join('user_profile', 'user_profile.id = blog_comment.user_id');
        $query = $this->db->get('blog_comment');
        return $query->result();
    }

    /**
     * Approve a comment
     *
     * @param integer $comment_id The ID of the comment
     */
    function approve_comment($comment_id) {
        $this->db->where('comment_id', $comment_id);
        $this->db->update('blog_comment', array('moderate' => 0));

        $this->load->model('event_model');
        $event_model = new event_model();
        $comment = $this->get_comment($comment_id);

        $event_model->add_event('user', $comment->user_id, 'news_comment', $comment_id);
    }

    /**
     * Update an existing comment item
     *
     * @param object $comment The new comment details
     */
    function update_comment($comment) {
        $comment_id       = $comment->comment_id;
        $this->db->update('blog_comment', $comment, array('comment_id'=>$comment_id));
    }

    /**
     * Delete and existing comment item
     *
     * @param integer $comment_id The ID of the comment
     */
    function delete_comment($comment_id) {
        $this->db->delete('blog_comment', array('comment_id' => $comment_id));
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->delete_events('news_comment', $comment_id);
    }

     /**
     * Get a specific comment on the blog
     *
     * @param integer $comment_id The ID of the comment
     * @return object The details of the comment
     */
    function get_comment($comment_id) {
        $comment = false;
        $this->db->select('comment_id,
                           blog_comment.body AS body,
                           blog_comment.user_id AS user_id,
                           user_profile.fullname AS fullname,
                           blog_post.title AS news_title,
                           blog_comment.post_id AS post_id');
        $this->db->join('blog_post', 'blog_post.post_id= blog_comment.post_id');
        $this->db->join('user_profile', 'user_profile.id = blog_comment.user_id',
                        'left');
        $this->db->join('user', 'user.id= user_profile.id');
        $this->db->where('comment_id', $comment_id);
        $this->db->where('user.banned', 0);
        $query = $this->db->get('blog_comment');
        if ($query->num_rows() !=  0 ) {
            $comment = $query->row();
        }

        return $comment;
    }
}
