<?php
/**
 * SinaWeibo client
 *
 * Most api are inspired from official client, rewrite with PECL-OAuth and latest SinaWeibo REST API.
 *
 * Note:
 * 客户端的API接口来自 http://open.t.sina.com.cn/wiki/index.php/API文档
 * 不过由于这里的API文档部分语焉不详,前后矛盾,因此需要高级授权的接口因无法测试,故无法保证实际运行正确
 *
 * @author nightsailer @nightsailer
 * @copyright Copyright 2010, Pan Fan(aka nightsailer). (http://nightsailer.com/)
 * @license GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 */
class SinaWeibo_Client {
    private $oauth;

    /**
     * constructor
     *
     * @param string $app_key
     * @param string $app_secret
     * @param string $access_token
     * @param string $access_token_secret
     */
    public function __construct($app_key,$app_secret,$access_token,$access_token_secret) {
        $this->oauth = new SinaWeibo_OAuth($app_key,$app_secret,$access_token,$access_token_secret);
    }

    /**
     * 获取最新更新的公共微博消息
     *
     * @return array
     */
    public function public_timeline() {
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/public_timeline.json');
    }

    /**
     * 获取当前用户所关注用户的最新微博信息 (别名: statuses/home_timeline)
     *
     * @return array
     */
    public function home_timeline() {
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/public_timeline.json');
    }

    /**
     * 获取用户发布的微博信息列表
     *
     * @param array $args
     * @return array
     */
    public function user_timeline($params = array()) {
        $default = array('page' => 1, 'count' => 20,'uid_or_name' => null);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/user_timeline.json',$params+$default);
    }

    /**
     * Alias of home_timeline
     *
     * @return array
     */
    public function friends_timeline() {
        return $this->home_timeline();
    }

    /**
     * 返回最新n条@我的微博
     *
     * 请求参数
     * since_id. 可选参数. 返回ID比数值since_id大（比since_id时间晚的）的提到。
     * max_id. 可选参数. 返回ID不大于max_id(时间不晚于max_id)的提到。
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200，默认为20。
     * page. 可选参数. 返回结果的页序号。注意：有分页限制。
     *
     * @param array $params 请求参数
     * @return array
     */
    public function mentions($params = array()) {
        $default = array('count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/mentions.json',$params+$default);
    }

    /**
     * 按时间顺序返回最新n条发送及收到的评论。类似微博的friends_timeline接口
     *
     * 请求参数:
     *  since_id: 可选参数（评论ID）. 只返回ID比since_id大（比since_id时间晚的）的评论。
     *  max_id: 可选参数（评论ID）. 返回ID不大于max_id的评论。
     *  count: 可选参数. 每次返回的最大记录数，不大于200，默认20。
     *  page: 可选参数. 返回结果的页序号。注意：有分页限制。
     *
     * @param array $params 请求参数
     * @return array
     */
    public function comments_timeline($params = array()) {
        $default = array('page' => 1, 'count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/comments_timeline.json',$params+$default);
    }

    /**
     * 发出的评论
     *
     * 请求参数
     * since_id：可选参数（评论ID）. 只返回比since_id大（比since_id时间晚的）的评论
     * max_id: 可选参数（评论ID）. 返回ID不大于max_id的评论。
     * count: 可选参数. 每次返回的最大记录数，最多返回200条，默认为20。
     * page： 可选参数. 分页返回。注意：最多返回200条分页内容。
     *
     * @param array $params
     * @return array
     */
    public function comments_by_me($params = array()) {
        $default = array('page' => 1, 'count' => 20);
        return $this->oauth->get( 'http://api.t.sina.com.cn/statuses/comments_by_me.json',$params+$default);
    }

    /**
     * 收到的评论
     *
     * 请求参数
     *  since_id. 可选参数 评论ID. 返回ID比数值since_id大（比since_id时间晚的）的评论。
     *  max_id. 可选参数 评论ID. 返回ID不大于max_id(时间不晚于max_id)的评论。
     *  count. 可选参数. 每次返回的最大记录数（即页面大小），最多返回200条，默认为20。
     *  page. 可选参数. 返回结果的页序号。注意：有分页限制。
     *
     * @param array $params 请求参数
     * @return array
     */
    public function comments_to_me($params = array()) {
        $default = array('page' => 1, 'count' => 20);
        return $this->oauth->get( 'http://api.t.sina.com.cn/statuses/comments_to_me.json',$params+$default);
    }

    /**
     * 返回指定微博的最新n条评论
     *
     * 可选参数
     *  count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200，默认为20。
     *  page. 可选参数. 返回结果的页序号。注意：有分页限制。
     *
     * @param int $id 微博ID
     * @param array $params 可选参数
     * @return void
     */
    public function get_comments_by_id($id,$params=array()) {
        $default = array('page' => 1, 'count' => 20);
        $params['id'] = $id;
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/comments.json',$params+$default);
    }

    /**
     * 批量统计微博的评论数，转发数，一次请求最多获取100个。
     *
     * @param string $ids 微博ID号列表，用逗号隔开
     * @return array
     */
    public function get_count_info_by_ids($ids) {
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/counts.json',array('ids' => $ids));
    }

    /**
     * 获取当前用户Web未读消息数，包括@我的, 新评论，新私信，新粉丝数。
     *
     * 请求参数
     *  with_new_status 可选参数，默认为0。1表示结果包含是否有新微博，0表示结果不包含是否有新微博。
     *  since_id 可选参数 参数值为微博id，返回此条id之后，是否有新微博产生，有返回1，没有返回0
     *
     * @param array $params
     * @return array
     */
    public function get_unread($params = array()) {
        $default = array('with_new_status' => 0);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/unread.json',$params+$default);
    }
    /**
     * 将当前用户指定类型的未读消息数清0。
     *
     * @param string $counter_type 需要清零的计数类别，值为下列四个之一:'comment'--评论数，'mention'--@数，'dm'--私信数，'follower'--关注我的数。
     * @return bool
     */
    public function reset_count($counter_type) {
        $type_constant = array('comment' => 1,'mention' => 2,'dm' => 3,'follower' => 4);
        if (!in_array($counter_type,$type_constant)) {
            return false;
        }
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/reset_count.json',
            array('type' => $type_constant[$counter_type]));
    }
    /**
     * 获取单条ID的微博信息，作者信息将同时返回。
     *
     * @param string $id 要获取已发表的微博ID
     * @return array
     */
    public function show_status($id) {
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/show/'.$id.'.json');
    }

    /**
     * 单条微博的Web访问路径。可以通过此url跳转到微博对应的Web网页。
     *
     * @param int $user_id 微博发布人的uid
     * @param int $id 要获取已发表的微博ID
     * @return string
     */
    public function status_url($user_id,$id) {
        return 'http://api.t.sina.com.cn/statuses/'.$user_id.'/'.$id;
    }

    /**
     * 发布一条微博信息。请求必须用POST方式提交。为防止重复，发布的信息与当前最新信息一样话，将会被忽略。
     *
     * 可选的参数
     *  in_reply_to_status_id. 可选参数，@ 需要回复的微博信息ID, 这个参数只有在微博内容以 @username 开头才有意义。（即将推出）。
     *  lat. 可选参数，纬度，发表当前微博所在的地理位置，有效范围 -90.0到+90.0, +表示北纬。只有用户设置中geo_enabled=true时候地理位置才有效。(仅对受邀请的合作开发者开放)
     *  long. 可选参数，经度。有效范围-180.0到+180.0, +表示东经。(仅对受邀请的合作开发者开放)
     *
     * 使用说明
     *  如果没有登录或超过发布上限，将返回403错误
     *  如果in_reply_to_status_id不存在，将返回500错误
     *  系统将忽略重复发布的信息。每次发布将比较最后一条发布消息，如果一样将被忽略。因此用户不能连续提交相同信息
     *
     * @param string $text 微博内容
     * @param array $params 可选的参数
     * @return array
     */
    public function update($text,$params=array()) {
        $params['status'] = $text;
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/update.json',$params);
    }

    /**
     * 上传图片及发布微博信息。请求必须用POST方式提交。为防止重复，发布的信息与当前最新信息一样话，将会被忽略。目前上传图片大小限制为<1M。
     *
     * @param string $text 要更新的微博信息。必须做URLEncode,信息内容不超过140个汉字。支持全角、半角字符。
     * @param string $pic_path 仅支持JPEG,GIF,PNG图片,为空返回400错误。目前上传图片大小限制为<1M。
     * @return array
     */
    public function upload($text,$pic_path) {
        $params['pic'] = '@'.$pic_path;
        $params['status'] = $text;
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/upload.json',$params);
    }

    /**
     * Alias of destroy
     *
     * @param string $id
     * @return array
     */
    public function delete($id) {
        return $this->destroy($id);
    }
    /**
     * 删除微博。
     *
     * @param string $sid
     * @return array
     */
    public function destroy($id) {
        return $this->oauth->delete('http://api.t.sina.com.cn/statuses/destroy/'.$id.'.json');
    }

    /**
     * 转发一条微博信息。请求必须用POST方式提交
     *
     * @param string $id 转发的微博id
     * @param string $text 添加的转发信息。必须做URLEncode,信息内容不超过140个汉字,如不填则自动生成类似“转发 @author: 原内容”文字。
     * @return array
     */
    public function repost($id,$text=null) {
        $params['id'] = $id;
        if (!empty($text)) {
            $params['status'] = $text;
        }
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/repost.json',$params);
    }

    /**
     * 对一条微博信息进行评论
     *
     * @param string $id 要评论的微博id
     * @param string $comment 评论内容
     * @param string $cid 要评论的评论id
     * @return array
     */
    public function send_comment($id,$comment,$cid=null) {
        $params['id'] = $id;
        $params['comment'] = $comment;
        if ($cid) {
            $params['cid'] = $cid;
        }
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/comment.json',$params);
    }
    /**
     * 删除评论
     *
     * @param int $comment_id
     * @return array
     */
    public function delete_comment($comment_id) {
        return $this->oauth->delete('http://api.t.sina.com.cn/statuses/comment_destroy/'.$comment_id.'.json');
    }

    /**
     * 批量删除指定id列表的评论
     *
     * @param string $ids 想要删除评论的id，多个id之间用半角逗号分割，支持最多20个。
     * @return array
     */
    public function delete_comments_by_ids($ids) {
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/comment/destroy_batch.json',
            array('ids' => $ids));
    }
    /**
     * 对一条微博评论信息进行回复
     *
     * @param string $id 要评论的微博id
     * @param string $comment 评论内容
     * @param string $cid 要评论的评论id
     * @return array
     */
    public function reply($id,$comment,$cid) {
        $params['id'] = $id;
        $params['comment'] = $comment;
        $params['cid'] = $cid;
        return $this->oauth->post('http://api.t.sina.com.cn/statuses/reply.json',$params);
    }
    /**
     * 返回系统推荐的用户列表
     *
     * 分类列表:
     * default:人气关注
     * ent:影视名星
     * hk_famous:港台名人
     * model:模特
     * cooking:美食&健康
     * sport:体育名人
     * finance:商界名人
     * tech:IT互联网
     * singer:歌手
     * writer：作家
     * moderator:主持人
     * medium:媒体总编
     * stockplayer:炒股高手
     *
     * @param string $category 分类，可选参数，返回某一类别的推荐用户，默认为 default
     * @return array
     */
    public function hot_users($category='default') {
        $params['category'] = $category;
        return $this->oauth->get('http://api.t.sina.com.cn/users/hot.json',$params);
    }
    /**
     * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
     *
     * 请求参数:
     * user_id. 指定用户UID,主要是用来区分用户UID跟微博昵称一样，产生歧义的时候，特别是在用户账号为数字导致和用户Uid发生歧义。
     * screen_name. 指定微博昵称，主要是用来区分用户UID跟微博昵称一样，产生歧义的时候。
     *
     * @param string $uid_or_name 用户UID或昵称
     * @param array $params 请求参数
     * @return array
     */
    public function show_user($uid_or_name,$params=array()) {
        return $this->oauth->get('http://api.t.sina.com.cn/users/show/'.$uid_or_name.'.json',$params);
    }

    /**
     * 返回用户关注对象列表，并返回最新微博文章。按关注人的关注时间倒序返回，每次返回N个,通过cursor参数来取得多于N的关注人。当然也可以通过ID,nickname,user_id参数来获取其他人的关注人列表。
     *
     * 请求参数:
     * user_id. 要获取的UID
     * screen_name. 要获取的微博昵称
     * cursor. 选填参数. 单页只能包含100个关注列表，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多, 如果没有下一页，则next_cursor返回0
     * 的关注列表
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200,默认返回20。
     *
     * @param array $params
     * @return array
     */
    public function friends($params=array()) {
        $default = array('count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/friends.json',$params+$default);
    }

    /**
     * 返回用户的粉丝列表，并返回粉丝的最新微博
     *
     * 按粉丝的关注时间倒序返回，每次返回100个,通过cursor参数来取得多于100的粉丝。注意目前接口最多只返回5000个粉丝。
     *
     * 请求参数:
     *
     * user_id. 选填参数. 要获取的UID
     * o 示例: http://api.t.sina.com.cn/statuses/followers.xml?user_id=1401881
     * screen_name. 选填参数. 要获取的微博昵称
     * o 示例: http://api.t.sina.com.cn/statuses/followers.xml?screen_name=101010
     * cursor. 选填参数. 单页只能包含100个粉丝列表，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的，如果没有下一页，则next_cursor返回0
     * 粉丝列表 o 示例: http://api.t.sina.com.cn/statuses/followers/barackobama.xml?cursor=-1 o 示例: http://api.t.sina.com.cn/statuses/followers/barackobama.xml?cursor=1300794057949944903
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200,默认返回20。
     * o 示例: http://api.t.sina.com.cn/statuses/followers/bob.xml?&count=200
     * @param array $params 请求参数
     * @return array
     */
    public function followers($params=array()) {
        $default = array('count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/statuses/followers.json',$params+$default);
    }

    /**
     * 返回用户的最新n条私信，并包含发送者和接受者的详细资料。
     *
     * 请求参数:
     * since_id. 可选参数. 返回ID比数值since_id大（比since_id时间晚的）的私信。
     * max_id. 可选参数. 返回ID不大于max_id(时间不晚于max_id)的私信。
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200。
     * page. 可选参数. 返回结果的页序号。注意：有分页限制。
     * @param array $params 请求参数
     * @return array
     */
    public function dm_list($params=array()) {
        $default = array('page' => 1,'count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/direct_messages.json',$params+$default);
    }
    /**
     * 返回登录用户已发送最新20条私信。包括发送者和接受者的详细资料。
     *
     * 请求参数
     *  since_id. 可选参数. 返回ID比数值since_id大（比since_id时间晚的）的私信。
     *  max_id. 可选参数. 返回ID不大于max_id(时间不晚于max_id)的私信。
     *  count. 可选参数. 每次返回的最大记录数（即页面大小），不大于200。
     *  page. 可选参数. 返回结果的页序号。注意：有分页限制。
     *
     * @param array $params 请求参数
     * @return array
     */
    public function dm_sent_list($params=array()) {
        $default = array('page' => 1,'count' => 20);
        return $this->oauth->get('http://api.t.sina.com.cn/direct_messages/sent.json',$params);
    }
    /**
     * 发送一条私信
     *
     * 选填参数
     *  screen_name: 微博昵称
     *  user_id: 新浪UID
     *
     * @param string $id UID或微博昵称
     * @param string $text 消息内容
     * @param string $params 选填参数
     * @return void
     */
    public function send_dm($id,$text,$params=array()) {
        return $this->oauth->get('http://api.t.sina.com.cn/direct_messages/new/'.$id.'.json',$params);
    }

    /**
     * 按ID删除私信
     *
     * 操作用户必须为私信的接收人。
     *
     * @param string $dm_id 要删除的私信主键ID
     * @return array
     */
    public function delete_dm($dm_id) {
        return $this->oauth->delete('http://api.t.sina.com.cn/direct_messages/destroy/'.$dm_id.'.json');
    }
    /**
     * 批量删除当前用户的私信
     *
     * @param string $ids 想要删除私信的id，多个id之间用半角逗号分割，支持最多20个。
     * @return void
     */
    public function delete_dm_by_ids($ids) {
        return $this->oauth->post('http://api.t.sina.com.cn/direct_messages/destroy_batch.json',
            array('ids' => $ids));
    }

    /**
     * 关注一个用户
     *
     * 成功则返回关注人的资料，目前的最多关注2000人，失败则返回一条字符串的说明。如果已经关注了此人，则返回http 403的状态。关注不存在的ID将返回400。
     *
     * 可选参数:
     * user_id: 要关注的用户UID,主要是用在区分用户UID跟微博昵称一样，产生歧义的时候。
     * screen_name: 要关注的微博昵称,主要是用在区分用户UID跟微博昵称一样，产生歧义的时候。
     *
     * @param string $id 要关注的用户UID或微博昵称
     * @param string $params 可选参数
     * @return array
     */
    public function follow($id,$params=array()) {
        return $this->oauth->post('http://api.t.sina.com.cn/friendships/create/'.$id.'.json',$params);
    }

    /**
     * 取消关注某用户。成功则返回被取消关注人的资料，失败则返回一条字符串的说明。
     *
     * 可选参数:
     * user_id:要取消关注的用户UID,主要是用在区分用户UID跟微博昵称一样，产生歧义的时候。
     * screen_name. 必填参数. 要取消的微博昵称,主要是用在区分用户UID跟微博昵称一样，产生歧义的时候。
     *
     * @param string $id 要取消关注的用户UID或微博昵称
     * @param array $params 可选参数
     * @return void
     */
    public function unfollow($id,$params=array()) {
        return $this->oauth->post('http://api.t.sina.com.cn/friendships/destroy/'.$id.'.json',$params);
    }

    /**
     * 返回用户的好友(关注对象)uid列表
     *
     * 请求参数:
     *
     * user_id. 选填参数. 要获取的UID
     * screen_name. 选填参数. 要获取的微博昵称
     * cursor. 选填参数. 单页只能包含5000个id，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的关注列表
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于5000，默认返回500。
     *
     * 注:如果没有提供cursor参数，将只返回最前面的5000个关注id
     *
     * @param string $uid_or_name 要获取的用户的UID或微博昵称
     * @param string $params 可选的请求参数
     * @return array
     */
    public function get_friends_ids($uid_or_name,$params=array()) {
        return $this->oauth->get('http://api.t.sina.com.cn/friends/ids/'.$uid_or_name.'.json',$params);
    }

    /**
     * 返回用户的粉丝uid列表
     *
     * 请求参数:
     *
     * user_id. 选填参数. 要获取的UID
     * screen_name. 选填参数. 要获取的微博昵称
     * cursor. 选填参数. 单页只能包含5000个id，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的关注列表
     * count. 可选参数. 每次返回的最大记录数（即页面大小），不大于5000，默认返回500。
     *
     * 注意:目前接口最多只返回5000个粉丝。
     *
     * @param string $uid_or_name 要获取的用户的UID或微博昵称
     * @param string $params
     * @return array
     */
    public function get_followers_ids($uid_or_name,$params=array()) {
        return $this->oauth->get('http://api.t.sina.com.cn/followers/ids/'.$uid_or_name.'.json',$params);
    }

    /**
     * 返回两个用户关系的详细情况
     *
     * 请求参数:
     * 以下参数可不填写，如不填，则取当前用户
     *  source_id. 源用户UID
     *  source_screen_name. 源微博昵称
     * 下面参数必须选填一个:
     *  target_id. 要判断的目的用户UID
     *  target_screen_name. 要判断的目的微博昵称
     *
     * @param array $params 请求参数
     * @return array
     */
    public function is_followed($params=array()) {
        if (!isset($params['target_id']) && !isset($params['target_screen_name'])) {
            throw new SinaWeibo_Exception('Invalid arguments:target_id nor target_screen_name missing');
        }
        return $this->oauth->get('http://api.t.sina.com.cn/friendships/show.json',$params);
    }

    /**
     * 判断用户身份是否合法且已经开通微博。
     *
     * 如果用户新浪通行证身份验证成功且用户已经开通微博则返回 http状态为 200；如果是不则返回401的状态和错误信息。
     *
     * @return array
     */
    public function verify_credentials() {
        return $this->oauth->get('http://api.t.sina.com.cn/account/verify_credentials.json');
    }

    /**
     * 关于API的访问频率限制。返回当前小时还能访问的次数。频率限制是根据用户请求来做的限制
     *
     * @return array
     */
    public function get_rate_status_limit() {
        return $this->oauth->get('http://api.t.sina.com.cn/account/rate_limit_status.json');
    }

    /**
     * 清除已验证用户的session，退出登录，并将cookie设为null。
     *
     * @return array
     */
    public function end_session() {
        return $this->oauth->post('http://api.t.sina.com.cn/account/end_session.json');
    }
    /**
     * 更新用户头像
     *
     * 图片必须为小于700K的有效的GIF,JPG,或PNG图片.如果图片大于500像素将按比例缩放。
     *
     * @param string $pic_path 图片路径
     * @return array
     */
    public function update_avatar($pic_path) {
        $params = array();
		$params['image'] = "@".$pic_path;
		return $this->oauth->post('http://api.t.sina.com.cn/account/update_profile_image.json',$params,true);
    }

    /**
     * 自定义微博页面的参数。只会修改参数更新项。
     *
     * profile 必须有一下参数中的一个或多个，参数值为字符串. 进一步的限制，请参阅下面的各个参数描述.
     *   name. 昵称，可选参数.不超过20个汉字
     *   gender 性别，可选参数. m,男，f,女。
     *   province 可选参数. 参考省份城市编码表
     *   city 可选参数. 参考省份城市编码表,1000为不限
     *   description. 可选参数. 不超过160个汉字.
     *
     * @param array $profile 要更新的选项参数
     * @return array
     */
    public function update_profile($profile=array()) {
        return $this->oauth->post( 'http://api.t.sina.com.cn/account/update_profile.json',$profile);
    }

    /**
     * 注册新浪微博用户接口，该接口为受限接口（只对受邀请的合作伙伴开放）。
     *
     * 注册信息有以下参数中的一个或多个，参数值为字符串. 进一步的限制，请参阅下面的各个参数描述.
     *  nick. 昵称，必须参数.不超过20个汉字
     *  gender 性别，必须参数. m,男，f,女。
     *  password 密码 必须参数.
     *  email 注册邮箱 必须参数，需要保持与当前网站同域，如：在abc.com下注册的用户需使用***@abc.com的邮箱。
     *  province 可选参数. 参考省份城市编码表
     *  city 可选参数. 参考省份城市编码表,1000为不限
     *  ip 必须参数，注册用户用户当前真实的IP。
     *
     * @param string $app_key 应用的AppKey
     * @param array $params 注册信息数组
     * @return void
     */
    public function register_account($app_key,$params=array()) {
        if (!isset($params['ip']) || !isset($params['email']) || isset($params['nick']) 
            || !isset($params['password'])) {
            throw new SinaWeibo_Excpetion('Invalid register parameters,missing something');
        }
        $params['source'] = $app_key;
        return $this->oauth->post('http://api.t.sina.com.cn/account/register.json',$params);
    }
    /**
     * 二次注册微博的接口
     *
     * 该接口为受限接口（只对受邀请的合作伙伴开放）。
     *
     * 注册信息有以下参数中的一个或多个，参数值为字符串. 进一步的限制，请参阅下面的各个参数描述.
     *
     *  uid 用户UID，必选参数
     *  nickname 昵称，必须参数.不超过20个汉字
     *  gender 性别，必须参数. m,男，f,女。
     *  password 密码 必须参数.
     *  email 注册邮箱 必须参数，需要保持与当前网站同域，如：在abc.com下注册的用户需使用***@abc.com的邮箱。
     *  province 可选参数. 参考省份城市编码表
     *  city 可选参数. 参考省份城市编码表,1000为不限
     *  ip 必须参数，注册用户用户当前真实的IP。
     *
     * @param string $app_key 应用的AppKey
     * @param array $params 注册信息数组
     * @return array
     */
    public function active_account($app_key,$params=array()) {
        if (!isset($params['uid']) || !isset($params['ip']) || !isset($params['email']) || 
            isset($params['nick']) || !isset($params['password'])) {
            throw new SinaWeibo_Excpetion('Invalid register parameters,missing something');
        }
        $params['source'] = $app_key;
        return $this->oauth->post('http://api.t.sina.com.cn/account/activate.json',$params);
    }

    /**
     * 测试平台状态
     *
     * 返回HTTP状态码为200,并返回字符串OK。
     *
     * @return array
     */
    public function test() {
        return $this->oauth->get('http://api.t.sina.com.cn/help/test.json');
    }

    /**
     * 返回用户的发布的最近20条收藏信息，和用户收藏页面返回内容是一致的。
     *
     * @param bool $page 返回结果的页序号。
     * @return array
     */
    public function get_favorites($page=false) {
        $params = array();
        if ($page) {
            $params['page'] = $page;
        }
        return $this->oauth->get('http://api.t.sina.com.cn/favorites.json',$params);
    }

    /**
     * 收藏一条微博信息
     *
     * @param mixed $id 收藏的微博id
     * @return array
     */
    public function add_favorite($id) {
        $params = array();
        $params['id'] = $id;
        return $this->oauth->post('http://api.t.sina.com.cn/favorites/create.json',$params);
    }

    /**
     * 删除微博收藏。
     *
     * 注意：只能删除自己收藏的信息
     *
     * @param mixed $sid 要删除的收藏微博信息ID.
     * @return array
     */
    public function delete_favorite($id) {
        return $this->oauth->delete('http://api.t.sina.com.cn/favorites/destroy/'.$id.'.json');
    }
    /**
     * 批量删除当前登录用户的收藏
     *
     * 出现异常时，返回HTTP400错误。
     * @param string $ids 想要删除收藏微博的id，多个id之间用半角逗号分割，支持最多20个
     * @return array
     */
    public function delete_favorites_by_ids($ids) {
        $params = array('ids' => $ids);
        return $this->oauth->post('http://api.t.sina.com.cn/favorites/destroy_batch.json',$params);
    }

    public function oauth() {
        return $this->oauth;
    }
}