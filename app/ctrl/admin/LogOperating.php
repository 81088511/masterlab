<?php

namespace main\app\ctrl\admin;


use main\app\classes\LogOperatingLogic;
use main\app\ctrl\BaseAdminCtrl;
use main\app\model\LogOperatingModel;




/**
 * 系统操作日志控制器
 */
class LogOperating extends BaseAdminCtrl
{

    /**
     * 操作日志入口页面
     */
    public function index()
    {
        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'log';
        $data['left_nav_active'] = 'log_operating';
        $data['actions'] = LogOperatingModel::getActions() ;
        $this->render('gitlab/admin/log_operating_list.php' ,$data );

    }

    public function filter($username = '',$action = '',$remark = '',$page = 1,$page_size = 20)
    {
        $pageSize = intval($page_size);
        $username = trimStr($username);

        $logLogic = new LogOperatingLogic();
        $ret = $logLogic->filter($username, $action, $remark, $page, $pageSize);
        list( $logs, $total ) = $ret;

        unset($logLogic);

        $data['total'] = $total;
        $data['pages'] = ceil($total / $pageSize);
        $data['page_size'] = $pageSize;
        $data['page'] = $page;
        $data['logs'] = array_values($logs);
        $this->ajaxSuccess('', $data);
    }


    /**
     * 日志细节
     */
    public function get( $id )
    {
        if( empty($id) )
        {
            $this->ajaxFailed(' id_is_empty ', [], 600);
        }

        $logModel = new LogOperatingModel();
        $log = $logModel->getById( (int)$id );

        $preData = $log['pre_data'];
        $curData = $log['cur_data'];

        $detail = [];

        if ( empty($preData) || empty($curData))
        {
            return $detail;
        }

        $i = 0;
        foreach ($preData as $key=>$val)
        {
            $detail[$i]['field'] = $key;
            $detail[$i]['before']  =  $val;
            $detail[$i]['now'] = $curData[$key];
            $detail[$i]['code'] = $val!=$curData[$key]?1:0;
            $i++;
        }

        $data['detail'] = $detail;
        $this->ajaxSuccess('', $data);
    }

}
