<?php

/**
*
* posey: Normal and Special Ranks (nasr)
* phpBB3.1 Extension Package
* @copyright (c) 2015 posey [ www.godfathertalks.com ]
* @license GNU General Public License v2 [ http://opensource.org/licenses/gpl-2.0.php ]
*
*/

namespace posey\nasr\core;

class extra_rank
{
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\user */
	protected $user;

	/** @var String phpBB Root path */
	protected $phpbb_root_path;
	
	/**
	* Constructor
	*
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\template\template $template
	* @param \phpbb\user
	* @param string $root_path
	* @access public
	*/
	
	public function __construct(
		\phpbb\config\config $config, 
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$phpbb_root_path
		)
	{
		$this->config			= $config;
		$this->db				= $db;
		$this->template			= $template;
		$this->user				= $user;
		$this->phpbb_root_path 	= $phpbb_root_path;
	}
	
	public function get_extra_user_rank($user_id)
	{
		// Grab user's current rank and post count
		$user_sql = 'SELECT user_rank, user_posts
					 FROM '. USERS_TABLE .'
					 WHERE user_id = '. $user_id;
		$user_results = $this->db->sql_query($user_sql);
		$user_info = $this->db->sql_fetchrow($user_results);
		$this->db->sql_freeresult($user_results);
		$user_post_count = (int) $user_info['user_posts'];
		
		// Select all non-special ranks
		$normal_sql = 'SELECT *
						FROM '. RANKS_TABLE .'
						WHERE rank_special != 1';
		$normal_results = $this->db->sql_query($normal_sql);
		
		// Check if user's rank is a special rank
		$spec_sql = 'SELECT rank_special AS is_special
					FROM '. RANKS_TABLE .'
					WHERE rank_id = '. (int) $user_info['user_rank'];
		$special = $this->db->sql_query($spec_sql);
		$current_rank_special = (int) $this->db->sql_fetchfield('is_special');
	
		// Set up initial arary
		$extra_rank_data = array(
			'title' 	=> '',
			'img_src'	=> '',
			'img' 		=> '',
		);
		
		if ($current_rank_special == 1)
		{		
			if ($user_post_count != 0)
			{
				while($rank_row = $this->db->sql_fetchrow($normal_results))
				{
					if ($user_post_count >= $rank_row['rank_min'])
					{
						$extra_rank_data = array(
							'title'		=> $rank_row['rank_title'],
							'img_src'	=> (!empty($rank_row['rank_image'])) ? $this->phpbb_root_path . $this->config['ranks_path'] . '/' . $rank_row['rank_image'] : '',
							'img'		=> (!empty($rank_row['rank_image'])) ? '<img src="' . $extra_rank_data['img_src'] . '" alt="' . $rank_row['rank_title'] . '" title="' . $rank_row['rank_title'] . '" />' : '',
						);
					}
				}
			}
		}
		return $extra_rank_data;
	}
}


