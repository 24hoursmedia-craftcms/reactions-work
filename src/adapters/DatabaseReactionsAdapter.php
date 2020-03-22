<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 15/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\adapters;

use twentyfourhoursmedia\reactionswork\helpers\traits\AttributeNamesGeneratingTrait;
use twentyfourhoursmedia\reactionswork\models\Recording;
use twentyfourhoursmedia\reactionswork\ReactionsWork;
use twentyfourhoursmedia\reactionswork\records\Recording as RecordingRecord;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkService;

/**
 * Class DatabaseReactionsAdapter
 */
class DatabaseReactionsAdapter implements ReactionsAdapterInterface
{

    use AttributeNamesGeneratingTrait;

    /**
     * Get or create a recording record
     * @param int $elementId
     * @param int $siteId
     * @return RecordingRecord
     */
    private function getRecordingRecord(int $elementId, int $siteId, $saveOnNew = true): RecordingRecord
    {
        $record = RecordingRecord::find()
            ->andWhere('elementId=:elementId')
            ->andWhere('siteId=:siteId')
            ->addParams(['elementId' => $elementId, 'siteId' => $siteId])
            ->one();
        if (!$record) {
            $record = new RecordingRecord();
            $record->setAttributes([
                'elementId' => $elementId,
                'siteId' => $siteId
            ], false);
            $saveOnNew && $record->save(false);
        }
        return $record;
    }

    /**
     * @inheritDoc
     */
    public function getRecording(int $elementId, int $siteId) : Recording {
        $record = $this->getRecordingRecord($elementId, $siteId);
        $recording = new Recording();
        $attrs = [
            'siteId' => $record->attributes['siteId'],
            'elementId' => $record->attributes['elementId'],
        ];
        foreach (ReactionsWorkService::ALL_REACTION_HANDLES as $handle) {
            $countAttr = $this->createCountAttrName($handle);
            $usersAttr = $this->createUserIdsAttrName($handle);
            $attrs[$usersAttr] = $record->denormalizeIntArray($record->attributes[$usersAttr] ?? '');
            // counts from the database are not authorative, only count of voting users.
            $attrs[$countAttr] = count($attrs[$usersAttr]);
        }
        $recording->setAttributes($attrs, false);
        return $recording;
    }

    /**
     * @inheritDoc
     */
    public function register(string $reactionHandle, int $elementId, int $siteId, int $userId, $set = true): Recording
    {
        $reactionHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($reactionHandle);
        if (!in_array($reactionHandle, ReactionsWorkService::ALL_REACTION_HANDLES, true)) {
            throw new \RuntimeException('Invalid reaction handle ' . $reactionHandle);
        }
        $recording = $this->getRecording($elementId, $siteId);
        $recording->register($reactionHandle, $userId, $set);
        $this->save($recording);
        return $recording;
    }

    /**
     * @inheritDoc
     */
    public function toggle(string $reactionHandle, int $elementId, int $siteId, int $userId): Recording
    {
        $reactionHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($reactionHandle);
        if (!in_array($reactionHandle, ReactionsWorkService::ALL_REACTION_HANDLES, true)) {
            throw new \RuntimeException('Invalid reaction handle ' . $reactionHandle);
        }
        $recording = $this->getRecording($elementId, $siteId);
        $recording->toggle($reactionHandle, $userId);
        $this->save($recording);
        return $recording;
    }

    private function moveModelDataToRecord(Recording $model, RecordingRecord $record) {
        $modelAttrs = $model->getAttributes();
        foreach (ReactionsWorkService::ALL_REACTION_HANDLES as $handle) {
            $countAttr = $this->createCountAttrName($handle);
            $usersAttr = $this->createUserIdsAttrName($handle);
            $record->setAttribute($countAttr, $modelAttrs[$countAttr] ?? 0);
            $record->setAttribute($usersAttr, $record->normalizeIntArray($modelAttrs[$usersAttr] ?? ''));
        }
    }

    /**
     * @inheritDoc
     */
    public function save(Recording $recording): bool
    {
        $attrs = $recording->getAttributes(['siteId', 'elementId']);
        $record = $this->getRecordingRecord($attrs['elementId'], $attrs['siteId']);
        $this->moveModelDataToRecord($recording, $record);
        return $record->save(false);
    }

}