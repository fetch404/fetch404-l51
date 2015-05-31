<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\Forum\UpdateChannelPermissionsRequest;
use Fetch404\Core\Models\Channel;
use Fetch404\Core\Models\ChannelPermission;
use Fetch404\Core\Models\Role;
use Laracasts\Flash\Flash;

class ChannelPermissionManagerController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$channels = Channel::all();

		return view('core.admin.forums.permission-editor.channel.index', array(
			'channels' => $channels
		));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Channel $channel
	 * @return Response
	 */
	public function edit(Channel $channel)
	{
		//
		$groups = Role::lists('name', 'id');

		$createThreadIds = ChannelPermission::whereChannelId($channel->id)->wherePermissionId(1)->lists('role_id', 'role_id');

		$accessChannelIds = ChannelPermission::whereChannelId($channel->id)->wherePermissionId(21)->lists('role_id', 'role_id');
		$replyIds = ChannelPermission::whereChannelId($channel->id)->wherePermissionId(6)->lists('role_id', 'role_id');

		$createThreadIds = collect($createThreadIds);
		$createThreadIds = $createThreadIds->filter(function($item) {
			return $item != 2;
		});

		$replyIds = collect($replyIds);
		$replyIds = $replyIds->filter(function($item) {
			return $item != 2;
		});

		return view('core.admin.forums.permission-editor.channel.edit', array(
			'channel' => $channel,
			'accessChannel' => $accessChannelIds,
			'createThread' => $createThreadIds->toArray(),
			'reply' => $replyIds->toArray(),
			'groups' => $groups
		));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateChannelPermissionsRequest $request
	 * @return Response
	 */
	public function update(UpdateChannelPermissionsRequest $request)
	{
		//
		$channel = $request->route()->getParameter('channel');

		$accessChannel = $request->input('allowed_groups');
		$createThreads = $request->input('create_threads');
		$reply = $request->input('reply_to_threads');

		$createThreads = collect($createThreads);
		$createThreads = $createThreads->filter(function($item) {
			return $item != 2;
		});

		$reply = collect($reply);
		$reply = $reply->filter(function($item) {
			return $item != 2;
		});

		ChannelPermission::whereChannelId($channel->id)
			->where('permission_id', '=', 21)
			->orWhere('permission_id', '=', 1)
			->orWhere('permission_id', '=', 6)
			->delete();

		foreach($accessChannel as $id)
		{
			ChannelPermission::create(array(
				'channel_id' => $channel->id,
				'role_id' => $id,
				'permission_id' => 21
			));
		}

		foreach($createThreads as $id)
		{
			ChannelPermission::create(array(
				'channel_id' => $channel->id,
				'role_id' => $id,
				'permission_id' => 1
			));
		}

		foreach($reply as $id)
		{
			ChannelPermission::create(array(
				'channel_id' => $channel->id,
				'role_id' => $id,
				'permission_id' => 6
			));
		}

		Flash::success('Updated channel permissions!');

		return redirect(route('admin.forum.get.permissions.channels.edit', array(
			$channel
		)));
	}

}
