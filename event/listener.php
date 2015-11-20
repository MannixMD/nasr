<?php

/**
*
* posey: Normal and Special Ranks (nasr)
* phpBB3.1 Extension Package
* @copyright (c) 2015 posey [ www.godfathertalks.com ]
* @license GNU General Public License v2 [ http://opensource.org/licenses/gpl-2.0.php ]
*
*/

namespace posey\nasr\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event Listener
*/

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\user */
	protected $user;
	
	/** @var ContainerInterface */
	protected $phpbb_container;
	
	/** @var \posey\nasr\core\extra_rank */
	protected $extra_rank;

	/** @var String phpBB Root path */
	protected $phpbb_root_path;
	
	/**
	* Constructor
	*
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\template\template $template
	* @param \phpbb\user
	* @param ContainerInterface
	* @param \posey\nasr\core\extra_rank
	* @param string $root_path
	* @access public
	*/
	
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, $container, \posey\nasr\core\extra_rank $extra_rank, $phpbb_root_path
		)
	{
		$this->config			= $config;
		$this->db				= $db;
		$this->template			= $template;
		$this->user				= $user;
		$this->container		= $container;
		$this->extra_rank		= $container->get('posey.nasr.extra_rank');
		$this->phpbb_root_path 	= $phpbb_root_path;
	}
	
	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_view_profile'			=> 'viewprofile',
			'core.viewtopic_modify_post_row'		=> 'viewtopic',
			'core.ucp_pm_view_messsage'				=> 'viewpm',
		);
	}
	
	public function viewprofile($event)
	{
		$member = $event['member']; 
		$user_id = (int) $member['user_id'];
		
		$extra_rank_data = $this->extra_rank->get_extra_user_rank($user_id);
		
		$this->template->assign_vars(array(
			'EXTRA_RANK_TITLE'	 => $extra_rank_data['title'],
			'EXTRA_RANK_IMG'	 => $extra_rank_data['img'],
			'EXTRA_RANK_IMG_SRC' => $extra_rank_data['img_src'],
		));
	}
	
	public function viewtopic($event)
	{
		$poster_id = $event['poster_id'];
		$user_id = (int) $poster_id;

		$extra_rank_data = $this->extra_rank->get_extra_user_rank($user_id);
		
		$event['post_row'] = array_merge($event['post_row'], array(
			'EXTRA_RANK_TITLE'	 => $extra_rank_data['title'],
			'EXTRA_RANK_IMG'	 => $extra_rank_data['img'],
			'EXTRA_RANK_IMG_SRC' => $extra_rank_data['img_src'],
		));
	}
	
	public function viewpm($event)
	{
		$user_info = $event['user_info'];
		$user_id = (int) $user_info['user_id'];
		
		$extra_rank_data = $this->extra_rank->get_extra_user_rank($user_id);
		
		$this->template->assign_vars(array(
			'EXTRA_RANK_TITLE'	 => $extra_rank_data['title'],
			'EXTRA_RANK_IMG'	 => $extra_rank_data['img'],
			'EXTRA_RANK_IMG_SRC' => $extra_rank_data['img_src'],
		));
	}
}