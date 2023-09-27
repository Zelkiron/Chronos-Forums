<?php 
require_once('connecttodatabase.php');
class Post {
    //all the variables needed for inserting in the database
    private $category_id;
    private $topic_id;
    private $poster_id;
    private $post_order;
    private $content;
    private $reputation;

    //our $pdo variable for pdo queries
    private $pdo;

    //constructor
    function __construct($category_id, $topic_id, $poster_id, $post_order, $content) {
        $this->category_id = $category_id;
        $this->topic_id = $topic_id;
        $this->poster_id = $poster_id;
        $this->post_order = $post_order;
        $this->content = $content;
        $this->reputation = 0;

        $db = new ConnectToDatabase();
        $this->pdo = $db->connect();
    }

    //updates the topic's newest reply date to now and the number of replies
    private function updateOriginalTopicOnNewestReply($value) {
        $update_topic_information_query = $this->pdo->prepare('UPDATE posts SET date_new_reply = now(), replies = replies + :val WHERE topic_id = :topic_id');
        $update_topic_information_query->bindParam('val', $value);
        $update_topic_information_query->bindParam('topic_id', $this->topic_id);
        return $update_topic_information_query->execute();
    }

    private function updateCategoryIncrements($value) {
        $update_category_increments = $this->pdo->prepare('UPDATE categories SET posts = posts + 1 WHERE category_id = :category_id');
        $update_category_increments->bindParam('category_id', $this->category_id);
        return $update_category_increments->execute();
    }

    //increments the user's post count by a specified value (usually -1 or 1)
    private function updatePosterNumberOfPosts($value) {
        $update_user_post_count_query = $this->pdo->prepare('UPDATE users SET number_of_posts = number_of_posts + :val WHERE id = :id');
        $update_user_post_count_query->bindParam('val', $value);
        $update_user_post_count_query->bindParam('id', $this->poster_id);
        return $update_user_post_count_query->execute();
    }

    private function updatePosterReputation($value) {
        $update_poster_reputation_query = $this->pdo->prepare('UPDATE users SET reputation = reputation + :val WHERE id = :id');
        $update_poster_reputation_query->bindParam('val', $value);
        $update_poster_reputation_query->bindParam('id', $this->poster_id);
    }
    
    public function createPost() {
        $post_reply_query = $this->pdo->prepare("INSERT INTO posts (category_id, topic_id, poster_id, post_order, content, reputation, date_created) VALUES (:category_id, :topic_id, :poster_id, :post_order, :content, :reputation, now())");
        $post_reply_query->bindParam('category_id', $this->category_id);
        $post_reply_query->bindParam('topic_id', $this->topic_id);
        $post_reply_query->bindParam('poster_id', $this->poster_id);
        $post_reply_query->bindParam('post_order', $this->post_order);
        $post_reply_query->bindParam('content', $this->content);
        $post_reply_query->bindParam('reputation', $this->reputation);
        return $post_reply_query->execute() && $this->updateOriginalTopicOnNewestReply(1) && $this->updatePosterNumberOfPosts(1) && $this->updateCategoryIncrements(1);
    }

    public function deletePost($post_id, $topic_id, $poster_id) {
        //$post_reply_query = $this->pdo->prepare('DELETE FROM posts');
    }
    
    public function likePost($post_id, $poster_id, $post_liker_id) {
        
    }

    //GETTERS
    public function getCategoryId() {
        return $this->category_id;
    }
    public function getTopicId() {
        return $this->topic_id;
    }
    public function getPosterId() {
        return $this->poster_id;
    }
    public function getPostOrder() {
        return $this->post_order;
    }
    public function getContent() {
        return $this->content;
    }
    public function getReputation() {
        return $this->reputation;
    }
}
?>