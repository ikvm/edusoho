<?php

namespace Biz\System\Service\Impl;

use Biz\BaseService;
use Topxia\Common\PluginToolkit;
use Topxia\Service\Common\Logger;
use Biz\System\Service\LogService;

class LogServiceImpl extends BaseService implements LogService
{
    public function info($module, $action, $message, array $data = null)
    {
        return $this->addLog('info', $module, $action, $message, $data);
    }

    public function warning($module, $action, $message, array $data = null)
    {
        return $this->addLog('warning', $module, $action, $message, $data);
    }

    public function error($module, $action, $message, array $data = null)
    {
        return $this->addLog('error', $module, $action, $message, $data);
    }

    public function search($conditions, $order, $start, $limit)
    {
        $conditions = $this->prepareSearchConditions($conditions);

        switch ($order) {
            case 'created':
                $order = array('createdTime' => 'DESC');
                break;
            case 'createdByAsc':
                $order = array('createdTime' => 'ASC');
                break;

            default:
                throw $this->createServiceException('参数order不正确。');
                break;
        }

        $logs = $this->getLogDao()->search($conditions, $order, $start, $limit);

        foreach ($logs as &$log) {
            $log['data'] = empty($log['data']) ? array() : json_decode($log['data'], true);
            unset($log);
        }

        return $logs;
    }

    public function count($conditions)
    {
        $conditions = $this->prepareSearchConditions($conditions);
        return $this->getLogDao()->count($conditions);
    }

    protected function addLog($level, $module, $action, $message, array $data = null)
    {
        return $this->getLogDao()->create(array(
            'module'      => Logger::getModule($module),
            'action'      => $action,
            'message'     => $message,
            'data'        => empty($data) ? '' : json_encode($data),
            'userId'      => $this->getCurrentUser()->id,
            'ip'          => $this->getCurrentUser()->currentIp,
            'createdTime' => time(),
            'level'       => $level
        ));
    }

    public function analysisLoginNumByTime($startTime, $endTime)
    {
        return $this->getLogDao()->analysisLoginNumByTime($startTime, $endTime);
    }

    public function analysisLoginDataByTime($startTime, $endTime)
    {
        return $this->getLogDao()->analysisLoginDataByTime($startTime, $endTime);
    }

    public function getLogModuleDicts()
    {
        $moduleDicts = Logger::getLogModuleDict();
        $modules     = $this->getLogModules();

        $dealModuleDicts = array();
        foreach ($modules as $module) {
            if (in_array($module, array_keys($moduleDicts))) {
                $dealModuleDicts[$module] = $moduleDicts[$module];
            }
        }
        return $dealModuleDicts;
    }

    public function findLogActionDictsyModule($module)
    {
        $systemActions = Logger::systemModuleConfig();
        $pluginActions = Logger::pluginModuleConfig();

        $actions = array_merge($systemActions, $pluginActions);

        if (isset($actions[$module])) {
            return $actions[$module];
        }
        return array();
    }

    protected function getLogDao()
    {
        return $this->createDao('System:LogDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function prepareSearchConditions($conditions)
    {
        if (!empty($conditions['nickname'])) {
            $existsUser           = $this->getUserService()->getUserByNickname($conditions['nickname']);
            $userId               = $existsUser ? $existsUser['id'] : -1;
            $conditions['userId'] = $userId;
            unset($conditions['nickname']);
        }

        if (!empty($conditions['startDateTime']) && !empty($conditions['endDateTime'])) {
            $conditions['startDateTime'] = strtotime($conditions['startDateTime']);
            $conditions['endDateTime']   = strtotime($conditions['endDateTime']);
        } else {
            unset($conditions['startDateTime']);
            unset($conditions['endDateTime']);
        }

        if (empty($conditions['level']) || !in_array($conditions['level'], array('info', 'warning', 'error'))) {
            unset($conditions['level']);
        }

        return $conditions;
    }

    private function getLogModules()
    {
        $systemModules = array_keys(Logger::systemModuleConfig());
        $pluginModules = array_keys(Logger::pluginModuleConfig());

        $plugins = PluginToolkit::getPlugins();
        if (empty($plugins)) {
            return $systemModules;
        }
        $plugins = array_map('strtolower', array_keys($plugins));

        foreach ($pluginModules as $key => $module) {
            $formatModule = str_replace('_', '', $module);
            if (!in_array($formatModule, $plugins)) {
                unset($pluginModules[$key]);
            }
        }
        if (in_array('homework', $plugins)) {
            $pluginModules[] = 'exercise';
        }

        $modules = array_merge($systemModules, $pluginModules);

        return $modules;
    }
}
