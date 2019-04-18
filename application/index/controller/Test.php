<?php
namespace app\index\controller;

use think\Controller;
use think\Db;


class Test extends Controller
{
    public function index()
    {
        $userview=',515158.com';

         //动态链接数据库
        $res = Db::connect('db_config1')
            ->query("SELECT purview FROM toa_dep_group where jituanid='2' ");

        foreach ($res as $k=>$v){
            $jituanid[] = $v['purview'];
        }
        $str = implode(',', $jituanid);
        $arr = array_unique(explode(',', $str));
        $unionid = implode(',', $arr);

        if ($unionid){
            $result = Db::connect('db_config1')
            ->query("SELECT id,`name` FROM toa_union where id in(".$unionid.") ORDER BY id asc");


            $jtid='';
            $jtname='';
            foreach ($result as $rjt) {
                $jtid.=','.$rjt['id'];
                $jtname.=','.$rjt['name'];
            }
            $jtid=explode(',',$jtid); 
            $jtname=explode(',',$jtname); 
        }
       
        $jtidStr = trim(implode(',',$jtid),',');
        $blogArr =  Db::connect('db_config1')
                  ->query("SELECT id,`name`,jituanid FROM toa_department where father='0'  and jituanid in($jtidStr)  ORDER BY id Asc");
                  
        
        $userArr =  Db::connect('db_config1')
                 ->query("SELECT u.id,v.`name`,u.departmentid,u.jituanid FROM toa_user u,toa_user_view v where u.id=v.uid AND u.jituanid in($jtidStr)  ORDER BY u.id Asc LIMIT 100");

       

        $depIdArr = array_column($blogArr,'id');

        foreach($userArr as $k=>$v){
            $depArr = explode(',',trim($v['departmentid'],','));   
            $depArr = array_unique($depArr);
            $depArr = array_filter($depArr);
            $depArr = array_merge($depArr);

            
            if(array_key_exists($v['id'], $depIdArr)){

            }
        }
        
        $dep_jt_id = array_column($blogArr,'jituanid');
        foreach($result as $k=>$v){
            if(array_key_exists($v['id'],$dep_jt_id)){

            }
        }

        dump($result);
        dump($blogArr);
        dump($userArr);
        // echo $html;
        

        /*for($i=0;$i<sizeof($jtid);$i++){
            $blog =  Db::connect('db_config1')
                  ->query("SELECT id FROM toa_department where father='0'  and jituanid='".$jtid[$i]."'  ORDER BY id Asc LIMIT 1");
            
              
            if(!empty($blog)){
                if($blog[0]['id']!=''){
                    if($blog[0]['id']!=''){
                        $this->dep_data(0,$userview,$jtid[$i]);
                    }
    
                }
            }
        }*/
    }

    public function depPage()
    {
         //动态链接数据库
         $res = Db::connect('db_config1')
         ->query("SELECT purview FROM toa_dep_group where jituanid='2' ");

        foreach ($res as $k=>$v){
            $jituanid[] = $v['purview'];
        }
        $str = implode(',', $jituanid);
        $arr = array_unique(explode(',', $str));
        $unionid = implode(',', $arr);

        if ($unionid){
            $result = Db::connect('db_config1')
                ->query("SELECT id,`name` FROM toa_union where id in(".$unionid.") ORDER BY id asc");


            $jtid='';
            $jtname='';
            foreach ($result as $rjt) {
                $jtid.=','.$rjt['id'];
                $jtname.=','.$rjt['name'];
            }
            $jtid=explode(',',$jtid); 
            $jtname=explode(',',$jtname); 
        }
        $assign = [
            'result'=>$result
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    public function depAjax(){
        $unionid = input('unionid');

        $depArr =  Db::connect('db_config1')
                  ->query("SELECT id,`name`,jituanid FROM toa_department where father='0'  and jituanid = $unionid  ORDER BY id Asc");
        
        
        return json_encode($depArr);
    }

    public function depAjax2(){
        $depid = input('depid');


        $depArr = Db::connect('db_config1')
            ->query("SELECT id,`name`,jituanid,father FROM toa_department WHERE FIND_IN_SET(id,queryChildrenAreaInfo($depid));");

        unset($depArr[0]);
        $depArr = array_merge($depArr);
        echo  json_encode($depArr);
    }

    public function unionDep()
    {
        $unionid = input('unionid');
        $depArr = Db::connect('db_config1')
            ->query("SELECT id,`name`,jituanid,father FROM toa_department WHERE FIND_IN_SET(id,queryChildrenAreaInfo($depid));");
    }

    //获取二级
    public function dep_data($fid=0,$userview,$jituanid){
        $row = Db::connect('db_config1')
            ->query("SELECT * FROM toa_department where father='$fid' and jituanid='".$jituanid."' ORDER BY number Asc");

        foreach($row as $v){
            $blog = Db::connect("db_config1")
                ->query("SELECT * FROM toa_department where father='".$v['id']."' and jituanid='".$jituanid."'  ORDER BY id Asc LIMIT 1");
            $user = Db::connect("db_config1")
                ->query("SELECT * FROM toa_user  WHERE departmentid LIKE '%,".$v['id'].",%' and jituanid='".$jituanid."' LIMIT 1");
    

            if(!empty($blog)) $blog = $blog[0];
            if(!empty($user)) $user = $user[0];
            dump($user);
            
            if(isset($blog['id'])){
                if($blog['id']!=''){
                    $this->dep_data($v['id'],$userview,$jituanid);
                }
            }
            if(isset($user['id'])){
                if($user['id']!=''){
                    $this->dep_data_user($v['id'],$userview,$jituanid);
                }
            }
            
        }
    }
    //获取成员
    public function dep_data_user($fid=0,$userview,$jituanid){
       
       
        $row = Db::connect('db_config1')
             ->query("SELECT a.id,b.name FROM toa_user a,toa_user_view b WHERE a.id=b.uid and a.departmentid LIKE '%,".$fid.",%' and a.jituanid='".$jituanid."' order by a.numbers asc");

    }


    public function addUser(){
        exit;
        $arr = [];
        for($i=1;$i<10000;$i++){
            $jituanid = $i%20==0?26:$i%20+6;
            $departmentid = $i%200==0?219:$i%200+19;
            $arr[$i] = [
                'username'=>'test'.$i,
                'password'=>md5('123456'),
                'jituanid'=>$jituanid,
                'departmentid'=>','.$departmentid.',',
                'numbers'=>'999'
            ];
        }

     

        //动态链接数据库
        $a = Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 数据库连接DSN配置
            'dsn'         => '',
            // 服务器地址
            'hostname'    => 'localhost',
            // 数据库名
            'database'    => 'shuj',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => '123',
            // 数据库连接端口
            'hostport'    => '',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => 'toa_',
        ])
        ->table('toa_user')
        ->data($arr)
        ->limit(500)
        ->insertAll();

        echo $a;
    }   


    public function addUserView(){
        exit;
        $arr = [];
        for($i=1;$i<10000;$i++){
            $jituanid = $i%20==0?26:$i%20+6;
            $departmentid = $i%200==0?219:$i%200+19;
            $arr[$i] = [
                'name'=>'test'.$i.'姓名',
                'uid'=>$i+21,
                'jituanid'=>$jituanid
            ];
        }

        //动态链接数据库
        $a = Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 数据库连接DSN配置
            'dsn'         => '',
            // 服务器地址
            'hostname'    => 'localhost',
            // 数据库名
            'database'    => 'shuj',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => '123',
            // 数据库连接端口
            'hostport'    => '',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => 'toa_',
        ])
        ->table('toa_user_view')
        ->data($arr)
        ->limit(500)
        // ->fetchSql(true)
        ->insertAll();

        echo $a;
    }

    public function addUnion(){
        $arr = [];
        for($i=1;$i<201;$i++){
            $jituanid = $i%20==0?26:$i%20+6;
            $arr[$i] = [
                'name'=>'test'.$jituanid.'部门'.ceil($i/10),
                'jituanid'=>$jituanid,
                'father'=>0,
                'persno1'=>0,
                'persno2'=>0,
                'persno3'=>0,
                'number'=>0
            ];
        }

         //动态链接数据库
         $a = Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 数据库连接DSN配置
            'dsn'         => '',
            // 服务器地址
            'hostname'    => 'localhost',
            // 数据库名
            'database'    => 'shuj',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => '123',
            // 数据库连接端口
            'hostport'    => '',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => 'toa_',
        ])
        ->table('toa_department')
        ->data($arr)
        ->limit(10)
        // ->fetchSql(true)
        ->insertAll();

        echo $a;
    }
    
    public function addUserDep()
    {
        exit;
        $res = Db::connect('db_config1')
            ->query("SELECT id,departmentid FROM toa_user LIMIT 101,10000");
            
        $sql = "INSERT INTO toa_user_dep_access (`uid`,`depid`) VALUES";
        foreach($res as $k=>$v){
            $depArr = explode(',',trim($v['departmentid'],','));   
            $depArr = array_unique($depArr);
            $depArr = array_filter($depArr);
            $depArr = array_merge($depArr);
            for($i=0;$i<count($depArr);$i++){
                if(isset($depArr[$i])){
                    $sql .= " (".$v['id'].",".$depArr[$i]."),";
                }
            }
        }
        $sql = trim($sql,',');
        echo $sql;
        $add = Db::connect('db_config1')
            ->query($sql);
    }

    public function test()
    {
        $time = time();
        echo $time;
    }
}
