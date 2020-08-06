<?php 
session_start();
?>
<link data-id="ki-publish" rel="stylesheet" href="<?=BASE_URL?>assets/css/modal.css'">

<div class="sm-modal-container">

	<div class="sm-modal-content sm-modal-content6" data-type="twitter">
		<div class="sm-modal6-body">
				<div class="twitter-profile-left-block">

					<ul class="twp-modal-nav">
	                    <li class="twp-twitter-btn" title="Twitter">
	                        <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" id="twp-linkedin-profile-img" ><a>Twitter</a>
	                    </li>
	                </ul>
				</div>

				<div class="twitter-profile-right-block">
					<div class="twp-right-body">
						<span class="twp-right-body-title">Twitter Profile</span>

						<div class="twp-right-body-info">
	                    	<ul>
			                    <li class="twp-username">Name: <?=$_SESSION['request_vars']['screen_name']?></li>
			                    <li class="twp-followers_count">Followers: <?=$_SESSION['request_vars']['followers']?></li>
			                    <li class="twp-friends_count">Friends: <?=$_SESSION['request_vars']['friends_count']?></li>
			                    <li class="twp-statuses_count">Tweets: <?=$_SESSION['request_vars']['statuses_count']?></li>
			                </ul>
	                    </div>

	                    <div class="twp-buttons_wrap" class="TwitterButton">
	                        <button class="cancel-button" data-modal="closeModal">Close</button>
	                    </div>

					</div>
				</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content6" data-type="vk">
		<div class="sm-modal6-body">
				<div class="twitter-profile-left-block">

					<ul class="twp-modal-nav">
	                    <li class="twp-twitter-btn" title="Twitter">
	                        <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" id="twp-linkedin-profile-img" ><a>Vk</a>
	                    </li>
	                </ul>
				</div>

				<div class="twitter-profile-right-block">
					<div class="twp-right-body">
						<span class="twp-right-body-title">Vk Profile</span>

						<div class="twp-right-body-info">
	                    	<ul>
			                    <li class="twp-username">Name: <?=$_SESSION['vk_user']['username']?></li>
			                    <li class="twp-followers_count">Followers: <?=$_SESSION['vk_user']['followers_count']?></li>
			                    <li class="twp-friends_count">Friends: <?=$_SESSION['vk_user']['friends_count']?></li>
			                </ul>
	                    </div>

	                    <div class="twp-buttons_wrap" class="TwitterButton">
	                        <button class="cancel-button" data-modal="closeModal">Close</button>
	                    </div>

					</div>
				</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content6" data-type="hootsuite">
		<div class="sm-modal6-body">
				<div class="twitter-profile-left-block">

					<ul class="twp-modal-nav">
	                    <li class="twp-twitter-btn" title="Twitter">
	                        <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" id="twp-linkedin-profile-img" ><a>Hootsuite</a>
	                    </li>
	                </ul>
				</div>

				<div class="twitter-profile-right-block">
					<div class="twp-right-body">
						<span class="twp-right-body-title">Hootsuite Profile</span>

						<div class="twp-right-body-info">
	                    	<ul>
			                    <li class="twp-username">Name: <?=$_SESSION['hootsuite_user']['username']?></li>
			                    <li class="twp-followers_count">Email: <?=$_SESSION['hootsuite_user']['email']?></li>
			                </ul>
	                    </div>

	                    <div class="twp-buttons_wrap" class="TwitterButton">
	                        <button class="cancel-button" data-modal="closeModal">Close</button>
	                    </div>

					</div>
				</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content6" data-type="buffer">
		<div class="sm-modal6-body">
				<div class="twitter-profile-left-block">

					<ul class="twp-modal-nav">
	                    <li class="twp-twitter-btn" title="Twitter">
	                        <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" id="twp-linkedin-profile-img" ><a>Buffer</a>
	                    </li>
	                </ul>
				</div>

				<div class="twitter-profile-right-block">
					<div class="twp-right-body">
						<span class="twp-right-body-title">Buffer Profile</span>

						<div class="twp-right-body-info">
	                    	<ul>
			                    <li class="twp-username">Name: <?=$_SESSION['buffer_user']->name?></li>
			                </ul>
	                    </div>

	                    <div class="twp-buttons_wrap" class="TwitterButton">
	                        <button class="cancel-button" data-modal="closeModal">Close</button>
	                    </div>

					</div>
				</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content5" data-type="linkedin">
		<div class="sm-modal5-body">
				<div id="linkedin-profile-left-block">

					<ul class="lnkp-modal-nav">
	                    <li id="lnkp-linkedin-btn" title="linkedin">
	                        <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png" id="lnkp-linkedin-profile-img"><a>Linkedin</a>
	                    </li>
	                </ul>
				</div>

				<div id="linkedin-profile-right-block">
					<div id="lnkp-right-body">

						<span id="lnkp-right-body-title">Linkedin Profile</span>

						<div id="lnkp-right-body-info">
	                    	<ul>
			                    <li id="lnkp-token">Token expire</li>
			                    <li id="lnkp-company-pages">Company Pages: 0</li>
			                </ul>
	                    </div>

<!-- 	                    <div class="lnkp-buttons_wrap" id="linkedinButton">
	                        <a  id="lnkp-updateToken"  href="<?=$linkedin_url?>">Update Token</a>
							<a  id="lnkp-getCompanyPages">Get Company Pages</a>
	                    </div> -->

					</div>
				</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content4" data-type="addBufferPost">
		<div class="sm-modal4-body">
	 		<span class="cm-font-lg">Post to</span>
	        <select id="bProfile">
	        	<?php 	
        			foreach ($buffer_service_name as $key) {
        				?>
        					<option value="<?=$key->profileId?>-<?=$key->serviceName?>"><?=$key->serviceName?></option>
        				<?php
        			}
	        	?>
	        </select>

	        <span class="cm-font-lg">Content</span>
	        <textarea class="form-control buffer-post" cols="3" rows="10" id="bPost"></textarea>

	        <span class="cm-font-lg">Please put your image url</span>
	        <input type="text" id="bPhoto">

	        <span class="cm-font-lg">Add media</span>
	        <input type="text" id="bMedia">

		    <div class="flex-box buffer-post-flex">
            	<button class="btn-blue" id="bPostAdd" style="min-width: 120px;">Post</button>
            </div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content4" data-type="addHootsuitePost">
		<div class="sm-modal4-body">
	 		<span class="cm-font-lg">Post to</span>
	        <select id="hProfile">
	        	<?php 	
        			foreach ($hootsuite_service_name as $key) {
        				?>
        					<option value="<?=$key->profileId?>-<?=$key->serviceName?>"><?=$key->serviceName?></option>
        				<?php
        			}
	        	?>
	        </select>

	        <span class="cm-font-lg">Content</span>
	        <textarea class="form-control hootsuite-post" cols="3" rows="10" id="hPost"></textarea>

	        <span class="cm-font-lg">Please put your image url</span>
	        <input type="text" id="hPhoto">

		    <div class="flex-box buffer-post-flex">
            	<button class="btn-blue" id="hPostAdd" style="min-width: 120px;">Post</button>
            </div>
		</div>
	</div>
	
	<div class="sm-modal-content sm-modal-content1" data-type="login">
		<div class="sm-modal-body">
			<div class="modal-left">
				<ul>
					<li class="cm-social-btn cm-linkedin-btn active-social-btn" data-type='linkedin'>
						<img src="<?=BASE_URL?>images/logo-15.png" alt="linkedIn">
						<span>Linkedin</span>
					</li>
					
					<li class="cm-social-btn cm-twitter-btn" data-type='twitter'>
						<img src="<?=BASE_URL?>images/logo-2.png" alt="">
						<span>Twitter</span>
					</li>
					<li class="cm-social-btn cm-wechat-btn" data-type='wechat'>
						<img width="55" src="<?=BASE_URL?>images/wechat-logo.png" alt="">
						<span>WeChat</span>
					</li>
					<li class="cm-social-btn cm-buffer-btn" data-type='buffer'>
						<img width="55" src="<?=BASE_URL?>images/buffer-logo.png" alt="">
						<span>Buffer</span>
					</li>
					<li class="cm-social-btn cm-vk-btn" data-type="vk">
						<img width="55" src="<?=BASE_URL?>images/VK_logo2.png" alt="">
						<span>VK</span>
					</li>
					<li class="cm-social-btn cm-hootsuite-btn" data-type='hootsuite'>
						<img width="55" src="<?=BASE_URL?>images/hootsuite.png" alt="hootsuite">
						<span>Hootsuite</span>
					</li>
				</ul>
			</div>
			<div class="modal-right">
				<div class="cm-social-body cm-linkedin-body active-body" data-id="linkedin">
					<span class="span-header">Add Linkedin Profile</span>
					<span class="span-text">To allow access to your Linkedin accounts, you must first give authorization from linkedin.com</span>
					<div class="modla-right-buttons">
						<a href="<?=BASE_URL?>includes/actions/actions.php?linkedin=true" class="btn-info">Connect with Linkedin</a>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>
				
				<div class="cm-social-body cm-twitter-body" data-id="twitter">
					<span class="span-header">Add Twitter Profile</span>
					<span class="span-text">To allow access to your Twitter accounts, you must first give authorization from Twitter.com</span>
					<div class="modla-right-buttons">
						<button class="btn-info">Connect with Twitter</button>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>

				<div class="cm-social-body cm-wechat-body" data-id="wechat">
					<span class="span-header">Add WeChat Profile</span>
					<span class="span-text">To allow access to your WeChat accounts, you must first give authorization from WeChat.com</span>
					<div class="modla-right-buttons">
						<button class="btn-info">Connect with WeChat</button>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>

				<div class="cm-social-body cm-buffer-body" data-id="buffer">
					<span class="span-header">Add Buffer Profile</span>
					<span class="span-text">To allow access to your Buffer accounts, you must first give authorization from Buffer.com</span>
					<div class="modla-right-buttons">
						<a href="<?=BASE_URL?>includes/actions/actions.php?buffer=true" class="btn-info">Connect with Buffer</a>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>

				<div class="cm-social-body cm-vk-body" data-id="vk">
					<span class="span-header">Add VK Profile</span>
					<span class="span-text">To allow access to your Vk accounts, you must first give authorization from vk.com</span>
					<div class="modla-right-buttons">
						<a href="<?=BASE_URL?>includes/actions/actions.php?vk=true" class="btn-info">Connect with VK</a>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>

				<div class="cm-social-body cm-linkedin-body" data-id="hootsuite">
					<span class="span-header">Add Hootsuite Profile</span>
					<span class="span-text">To allow access to your Hootsuite accounts, you must first give authorization from Hootsuite.com</span>
					<div class="modla-right-buttons">
						<a href="<?=BASE_URL?>includes/actions/actions.php?hootsuite=true" class="btn-info">Connect with Hootsuite</a>
						<button data-modal="closeModal" class="cancel-button">Cancel</button>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content2" data-type="post">
		<div class="sm-modal2-body">
			<div class="modal2-left">
				<span class="modal2-text">Post to</span>
				<div class="modal2-select-div">

					<div class="cm-avatar-box"></div>

					<div class="sm-more-options" id="openSmList">
						<button id="open-socnetwork-list" >More options</button>
						<span class="fa fa-chevron-down"></span>

						<ul id="social_network_list">
							<li class="social_network_item" data-soc="twitter">Twitter</li>
							<?php if (isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token'])) { ?>
								<li  class='social_network_item' data-soc='linkedin'>Linkedin</li>
							<?php } ?>
							<?php if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) { ?>
								<li  class='social_network_item' data-soc='vk'>Vk</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="contents sm-form-group">
					<span>Contents</span>
					<textarea name="" id="smPostContent" cols="30" rows="15" placeholder="Enter your text..."></textarea>
					<input type="text" id="smPostImg" placeholder="Paste image URL...">
				</div>
				<div class="publish-time">
					<span>Publish time</span>
					<div>
						<label for="immediately">
							<input type="radio" id="immediately" name="dateType" value="now" checked="true">
							Immediately
						</label>
						<label for="schedule">
							<input type="radio" id="schedule" name="dateType" value="later">
							Set Schedule
						</label>
					</div>
					<div id="dateTimePicker" style="margin-top: 3%; display: none;">
						<input type="datetime-local" id="yourDatePicker" style="margin-right: 3%">
						<!-- <input type="time" id="yourTimePicker" value="23:59"> -->
					</div>
				</div>
			</div>
			<div class="modal2-right">
				<div class="modal2-top">

					<div class="panel panel-info" id="twitter_preview" data-active='inactive' data-id="twitter" >
                        <div class="panel-heading twitter-bg">
                            <h3 class="panel-title"><i class="fa fa-twitter"></i></h3>
                        </div>
                        <div class="panel-body">
                            <div style="word-break: break-word;">
                                <img src="<?=$_SESSION['request_vars']['picture']?>" class="Page-2 pull-left">
                                <div class="Firstname-Lastname">
                                    <span><?=$_SESSION['username']?></span>
                                    <div class="days-ago">@<?=$_SESSION['request_vars']['screen_name']?></div>
                                </div>
                                <p class="cm-font-sm-bold" id="cm-font-sm-bold" data-soc="twitter" >
                                	Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dum Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dum</p>
                                <p class="preview_image" id="preview_image" data-soc="twitter"></p>
                            </div>
                        </div>
                    </div>


					<?php if ( isset($_SESSION['linkedin_user']['access_token']) && !empty($_SESSION['linkedin_user']['access_token']) && !empty($linkedin_user)  ) {?>
								<div class="panel panel-info" id="linkedin_preview" data-active='inactive' data-id="linkedin" >
			                        <div class="panel-heading linkedin-bg">
			                            <h3 class="panel-title">
			                            	<img src="http://wp.asd/wp-content/plugins/ki-publish/images/logo-15.png" alt="" class="panel-title-img">
			                            </h3>
			                        </div>
			                        <div class="panel-body">
			                            <div style="word-break: break-word;">
			                                <img src="<?=$_SESSION['linkedin_user']['picture']?>" class="Page-2 pull-left" style="max-width: 48px;padding: 10px;" >
			                                <div class="Firstname-Lastname">
			                                    <span><?php echo $_SESSION['linkedin_user']['firstname'].' '.$_SESSION['linkedin_user']['lastname']?></span>
			                                    <div class="days-ago">@<?=$_SESSION['request_vars']['screen_name']?></div> 
			                                </div>
			                                <p class="cm-font-sm-bold" id="cm-font-sm-bold-2" data-soc="linkedin">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dum Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dum</p>
			                                <p class="preview_image" id="preview_image-2" data-soc="linkedin"></p>
			                            </div>
			                        </div>
			                    </div>
							<?php
						}

					?>
				</div>
				<div class="modal2-bottom">
					<button class="btn-grey">Draft</button>
					<button class="btn-blue" id="addTwPost">Post</button>
					<img src="<?=BASE_URL?>images/Loading_icon.gif" width="38" height="38" id="get-users-loader" style="display: none;">
				</div>
			</div>
		</div>
	</div>
	
	<div class="sm-modal-content sm-modal-content3" data-type="add-tab">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Add a new tab</p>
			<div class="sm-form-group">
            	<label for="team-name" class="sm-font-md-gray">Tab name</label>
                <input type="text" name="name" class="sm-text-input" id="tab-name">
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="add_tab">Add Tab</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content3" data-type="add-team">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Add a new team</p>
			<div class="sm-form-group">
            	<label for="teamName" class="sm-font-md-gray">Team Name</label>
                <input type="text" name="name" class="sm-text-input" id="teamName">
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="addTeam">Add Team</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content7" data-type="add-stream">
		<div class="sm-modal-body7">
			<div class="sm-modal-body7-left">
				<ul>
					<li class="cm-social-btn cm-twitter-btn active-social-btn" data-type="stream" data-social="twitter">
						<img src="<?=BASE_URL?>images/logo-2.png" alt="">
						<span>Twitter</span>
					</li>
				</ul>
			</div>
			<div class="sm-modal-body7-right">
				<div class="tab-container">
					<div class="tab-header">
						<ul class="stream-tab">
							<li class="stream-tab-item active-tab" data-type="stream">Stream</li>
							<li class="stream-tab-item" data-type="search">Search</li>
							<li class="stream-tab-item" data-type="rss">RSS</li>
						</ul>
					</div>
					<div class="tab-body">
						<div class="stream-body stream-tab-body stream-active-body" data-type="stream">
							<p class="text-gray">Select a type of stream</p>
							<div class="stream-types-container">
								<ul>
									<li class="stream-type active-type" data-type="home">Home(page)</li>
									<li class="stream-type" data-type="inbox">Inbox(DM)</li>
									<li class="stream-type" data-type="my_tweets">My Tweets</li>
									<li class="stream-type" data-type="my_mentions">My Mentions</li>
									<li class="stream-type" data-type="user_likes">User Likes</li>
								</ul>
							</div>
						</div>
						<div class="search-body stream-tab-body" data-type="search">
							<p class="text-gray">Enter search query</p>
							<input type="text" class="stream-serch-input" id="search_phrase">
							<table>
								<tr>
									<th class="cm-font-md-bold-gray ">Query</th>
									<th class="cm-font-md-bold-gray ">Show results</th>
								</tr>
								<tr>
                                    <td class="cm-font-md-green">twitter search</td>
                                    <td class="cm-font-md-gray">containing both "twitter" and "search".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">“Twitter rock" </td>
                                    <td class="cm-font-md-gray">containing the exact phrase “Twitter rock".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">Batman OR Superman </td>
                                    <td class="cm-font-md-gray">containing either “Batman” or “Superman” (or both).</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">ant-man</td>
                                    <td class="cm-font-md-gray">containing “Ant” but not "man".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">#followfriday</td>
                                    <td class="cm-font-md-gray">containing the hashtag "followfriday".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">from:kisocial</td>
                                    <td class="cm-font-md-gray">sent from person “kisocial”.</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">to:invoke</td>
                                    <td class="cm-font-md-gray">sent to person "invoke".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">@avengers</td>
                                    <td class="cm-font-md-gray">referencing person “avengers”.</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">colbert since:2008-07-27</td>
                                    <td class="cm-font-md-gray">containing "colbert" sent since "2008-07-27"</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">w00t until:2008-07-27 </td>
                                    <td class="cm-font-md-gray">containing "w00t" sent up to "2008-07-27".</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">rainbow :) </td>
                                    <td class="cm-font-md-gray">containing “rainbow” with a positive attitude.</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">fail :( </td>
                                    <td class="cm-font-md-gray">containing "fail" and with a negative attitude.</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">Sausage ? </td>
                                    <td class="cm-font-md-gray">containing “Sausage” and asking a question.</td>
                                </tr>
                                <tr>
                                    <td class="cm-font-md-green">rainbows filter:links</td>
                                    <td class="cm-font-md-gray">rcontaining "rainbows" and linking to URLs.</td>
                                </tr>
							</table>
						</div>
						<div class="rss-body stream-tab-body" data-type="rss">
							<p class="text-gray">RSS feed name</p>
							<input type="text" id="rss_feed_name">
							<p class="text-gray">RSS URLs (comma separated)</p>
							<textarea id="rss_urls"></textarea>
						</div>
						<button class="btn-info" id="add_stream">Add stream</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content3" data-type="add-teammate">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Add new teammate</p>
			<div class="sm-form-group">
            	<label for="team-name" class="sm-font-md-gray">Search users</label>
                <input type="text" name="name" class="sm-text-input" id="teammateSearch" autocomplete="no">
                <ul id="userList">
                </ul>
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="addTeammate">Add Teammate</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content3" data-type="assign-team">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Assign this tab to Team</p>
			<div class="sm-form-group">
            	<label for="team-name" class="sm-font-md-gray">Find Team</label>
                <input type="text" name="name" class="sm-text-input" id="teamSearch" autocomplete="no">
                <ul id="teamList">
                </ul>
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="addTeamToTab">Assign Team</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content3" data-type="message">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Message</p>
			<div class="sm-form-group">
                <textarea id="messageText"></textarea>
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="sendMessage">Send</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="sm-modal-content sm-modal-content3" data-type="assign">
		<div class="sm-modal3-body">
			<p class="blue-modal-header">Assign this to...</p>
			<div class="sm-form-group">
				<label for="team-name" class="sm-font-md-gray">Find Team</label>
				<select id="assignmentUsers"></select>
			</div>
			<div class="modal-buttons">
				<button class="btn-blue" id="assignTeam">Assign</button>
				<button data-modal="closeModal" class="cancel-button">Cancel</button>
			</div>
		</div>
	</div>

	<div class="loader">
		
	</div>

</div>

