<?php

/*
* Buddypress component functions
*/
function sp_get_bp_current_component(){
	global $bp;
	return $bp->current_component;
}

/*
* Buddypress group functions
*/
function sp_get_bp_group_name(){
	global $bp;
	$bp_group = new BP_Groups_Group( $bp->groups->current_group->id );
	return $bp_group->name;
}

function sp_get_bp_group_description(){
	global $bp;
	$bp_group = new BP_Groups_Group( $bp->groups->current_group->id );
	return $bp_group->description;
}


/*
* Buddypress user functions
*/
function sp_get_bp_user_nicename(){
	global $bp;
	return $bp->displayed_user->userdata->user_nicename;
}
function sp_get_bp_user_registered(){
	global $bp;
	return $bp->displayed_user->userdata->user_registered;
}
function sp_get_bp_user_display_name(){
	global $bp;
	return $bp->displayed_user->userdata->display_name;
}
function sp_get_bp_user_fullname(){
	global $bp;
	return $bp->displayed_user->fullname;
}

/*
* Buddypress forum functions
*/
class sp_bp_forum_topic{
	public function __construct(){
		bp_has_forum_topics();
	}
	
	private function get_forum_arr_pos(){
		global $bp, $forum_template;	
		
		$topic_slug = $bp->action_variables[1];
		$topic_id = bp_forums_get_topic_id_from_slug( $topic_slug );
		
		for( $i=0; $i <= count( $forum_template->topics ) ; $i++ ){
			if($topic_id == $forum_template->topics[$i]->topic_id ) { 
				$array_pos = $i;      
			}
		}
		return $array_pos;		
	}
	
	private function get_last_post_id(){
		global $forum_template;	
		return $forum_template->topics[$this->get_forum_arr_pos()]->topic_last_post_id;
	}
	
	public function get_title(){
		global $forum_template;	
		return $forum_template->topics[$this->get_forum_arr_pos()]->topic_title;		
	}
	
	public function get_poster_name(){
		global $forum_template;	
		return $forum_template->topics[$this->get_forum_arr_pos()]->topic_poster_name;		
	}
	
	public function get_last_poster_name(){
		global $forum_template;	
		return $forum_template->topics[$this->get_forum_arr_pos()]->topic_last_poster_name;		
	}
	
	public function get_first_post_text(){
		global $forum_template;	
		$post = bb_get_first_post( $forum_template->topics[$this->get_forum_arr_pos()]->topic_id , false );
		return $post->post_text;
	}
	
	public function get_last_post_text(){
    	$post = bb_get_post( $this->get_last_post_id() );
		return $post->post_text;		
	}
	
}
function sp_get_bp_forum_topic_title(){
	$topic = new sp_bp_forum_topic();
	return $topic->get_title();
}
function sp_get_bp_forum_topic_poster_name(){
	$topic = new sp_bp_forum_topic();
	return $topic->get_poster_name();
}
function sp_get_bp_forum_topic_last_poster_name(){
	$topic = new sp_bp_forum_topic();
	return $topic->get_last_poster_name();
}
function sp_get_bp_forum_post_text(){
	$topic = new sp_bp_forum_topic();
	return $topic->get_first_post_text();
}
function sp_get_bp_forum_last_post_text(){
	$topic = new sp_bp_forum_topic();
	return $topic->get_last_post_text();
}

/*
* Buddypress universal functions
*/
function sp_get_bp_current_action(){
	global $bp;
	return $bp->current_action;
}


// Buddypress pages
function sp_init_bp_vars(){
	global $bp, $sp_bp_current_component, $forum_template, $forum_array_pos;
	
	$sp_bp_current_component = $bp->active_components[$bp->current_component];
	if ( !isset($sp_bp_current_component) ){ $sp_bp_current_component = $bp->current_component; }
	
	if ( isset( $bp->forums ) && $sp_bp_current_component == 'forums' || $bp->current_action == "forum"){
		 if( !isset($forum_template) ){
			 $forum_topic_id = bp_forums_get_topic_id_from_slug( $bp->action_variables[1] );
			 // $forum_template = new BP_Forums_Template_Forum( 'newest' , groups_get_groupmeta( $bp->groups->current_group->id, 'forum_id' ), false, -1,  -1, -1, 'all', false );
			 for ( $i=0; $i<=count($forum_template->topics); $i++ ){
				if ($forum_topic_id == $forum_template->topics[$i]->topic_id) { 
					$forum_array_pos = $i;      
				}
			  }
		 } else {
			$forum_template = new BP_Forums_Template_Forum( 'newest', groups_get_groupmeta( $bp->current_component->current->id, 'forum_id' ), false, 1, 1, 1, 'all', false );
			$forum_array_pos = 0;
		 }
	}
}


?>