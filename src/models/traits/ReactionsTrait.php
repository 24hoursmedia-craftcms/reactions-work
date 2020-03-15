<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 15/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\models\traits;

trait ReactionsTrait
{

    public $likeCount = 0;
    public $likeUserIds = [];

    public $loveCount = 0;
    public $loveUserIds = [];

    public $hahaCount = 0;
    public $hahaUserIds = [];

    public $wowCount = 0;
    public $wowUserIds = [];

    public $sadCount = 0;
    public $sadUserIds = [];

    public $angryCount = 0;
    public $angryUserIds = [];

    public $allCount = 0;
    public $allUserIds = [];
}