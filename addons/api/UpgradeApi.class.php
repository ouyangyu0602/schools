<?php
class UpgradeApi extends Api
{
    public function getVersion(){
        $versionInfo = array(
                                'version_code' => 2, //3=ts3,01=第1个版本
                                'upgrade_tips' => '有最新的软件包哦，亲快下载吧~',
                                'download_url' => 'http://download.thinksns.com/app/ThinkSNS_Android_V3.apk',
                                'must_upgrade' => 1,
                            );
        return $versionInfo;
    }
}