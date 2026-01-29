<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use Kkokk\Poster\Facades\Captcha;
use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\admin\AdminConfig;
use plugin\vatadmin\app\model\admin\AdminDict;
use plugin\vatadmin\app\model\admin\AdminNotice;
use plugin\vatadmin\service\tools\Aes;
use support\Container;
use support\Redis;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use Tinywan\Jwt\JwtToken;
use Vat\Validate;
use Webman\Event\Event;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminUser $model
 */
class UserController extends BaseController
{
    /**
     * 不需要登录的方法
     */
    protected $noNeedLogin = ['login','refreshToken', 'captcha'];

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminUser::class);
    }

    /***
     * 统一授权登陆
     * 采用JWT
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $captcha = $request->input('captcha');
        if(!$captcha){
            return $this->error('请先安全验证');
        }
        //检测账号密码
        try {
            $captchaInfo = json_decode(base64_decode($captcha), true);
            //检测验证
            $res = Captcha::type($captchaInfo['t'])->check($captchaInfo['k'], $captchaInfo['x'], 8, Redis::get($captchaInfo['k']));
            if(!$res){
                return $this->error('安全验证未通过');
            }
            $adminUserRs = $this->model::checkLogin($username, $password);
            $user = [
                'id'            => $adminUserRs->id,
                'name'          => $adminUserRs->name,
                'username'      => $adminUserRs->username,
            ];
        }catch (BadRequestHttpException $e){
            Event::emit('user.login', ['username' => $username, 'message' => $e->getMessage()]);
            throw new BadRequestHttpException($e->getMessage());
        }catch (\Throwable $e){
            Event::emit('user.login', ['username' => $username, 'message' => '登录失败']);
            throw new BadRequestHttpException('登录失败');
        }

        $jwtRs = JwtToken::generateToken($user);
        $userInfo = $this->model::userInfo($adminUserRs);
        $jwtRs = array_merge($jwtRs, $userInfo);
        //最后登录时间
        $adminUserRs->save(['last_login_time' => date('Y-m-d H:i:s')]);
        
        // 加载系统 配置和字典
        AdminConfig::refreshConfig();
        AdminDict::refreshCache();

        //登录事件
        Event::emit('user.login', ['username' => $username, 'message' => '登录成功']);
        return $this->ok('登录成功', $jwtRs);

    }

    /**
     * 用户退出
     */
    public function logout(Request $request){
        if(JwtToken::clear()){
            return $this->ok('退出成功');
        }
        return $this->error('退出失败');
    }

    /**
     * 修改密码
     */
    public function passwordChange(Request $request){
        $data['id'] = JwtToken::getCurrentId();
        $data['password_old'] = $request->input('password_old');
        $data['password_new'] = $request->input('password_new');
        $data['password_sure'] = $request->input('password_sure');

        $rs = $this->model::changePassword($data);
        if($rs){
            return $this->ok('操作成功');
        }

        return $this->error('操作失败');
    }

    /**
     * 用户信息
     */
    public function info(Request $request){
        $adminId = JwtToken::getCurrentId();
        $adminInfo = $this->model::find($adminId);
        $data = $this->model::userInfo($adminInfo);
        return $this->ok('获取成功',$data);
    }

    /**
     * 编辑信息
     * @param Request $request
     */
    public function editInfo(Request $request){
        Validate::setErrorHandler(BadRequestHttpException::class);
        $data = Validate::check($request->all(),[
            'name' => ['required' => '请输入名称'],
            'mobile' => '',
            'email' => '',
            'avatar' => '',
        ]);
        $adminInfo = $this->model->find(JwtToken::getCurrentId());
        $rs = $adminInfo->save($data);
        if($rs){
            $aes = new Aes(['iv' => md5($adminInfo->id, 16)]);
            $userInfo = [
                'id'        => $aes->encrypt($adminInfo->id),
                'name'      => $adminInfo->name,
                'username'  => $adminInfo->username,
                'mobile'    => $adminInfo->mobile,
                'email'     => $adminInfo->email,
                'avatar'    => $adminInfo->avatar,
                'roles'     => $adminInfo->roles,
                'noread'    => AdminNotice::getCountNoRead($adminInfo->id),
            ];
            return $this->ok('操作成功', $userInfo);
        }
        return $this->error('操作失败');
    }

    /**
     * 验证码
     * @param Request $request
     */
    public function captcha(Request $request){
        $picName = rand(1,9);
        # 自定义参数
        $params = [
            'src'           => base_path()."/plugin/vatadmin/resource/captcha/{$picName}.jpg",  // 背景图片，尺寸 340 * 191
            'im_width'      => 340, // 画布宽度
            'im_height'     => 251, // 画布高度
            'im_type'       => 'png', // png 默认 jpg quality 质量
            'quality'       => 80,    // jpg quality 质量
            'bg_width'      => 340, // 背景宽度
            'bg_height'     => 191, // 背景高度
            'slider_width'  => 50,  // 滑块宽度
            'slider_height' => 50,  // 滑块高度
            'slider_border' => 2,   // 滑块边框
            'slider_bg'     => 1,   // 滑块背景数量
        ];

        $type = 'slider';
        $data = Captcha::type($type)->config($params)->get();
        //缓存secret 1分钟
        Redis::set($data['key'], $data['secret']);
        Redis::expire($data['key'], 60);
        return $this->ok('ok',$data);
    }
}