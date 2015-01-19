<?php

/**
 * Handles Asset Image uploads
 *
 * @author Steve Bauman <sbauman@bwbc.gc.ca>
 */

namespace Stevebauman\Maintenance\Services\Asset;

use Dmyers\Storage\Storage;
use Stevebauman\Maintenance\Services\SentryService;
use Stevebauman\Maintenance\Services\Asset\AssetService;
use Stevebauman\Maintenance\Services\AttachmentService;
use Stevebauman\Maintenance\Services\BaseModelService;

class ImageService extends BaseModelService
{

    /**
     * @var AssetService
     */
    protected $asset;

    /**
     * @var AttachmentService
     */
    protected $attachment;

    /**
     * @var SentryService
     */
    protected $sentry;

    public function __construct(AssetService $asset, AttachmentService $attachment, SentryService $sentry)
    {
        $this->asset = $asset;
        $this->attachment = $attachment;
        $this->sentry = $sentry;
    }

    /**
     * Creates attachment records, attaches them to the asset images pivot table,
     * and moves the uploaded file into it's stationary position (out of the temp folder)
     *
     * @return mixed
     */
    public function create()
    {

        $this->dbStartTransaction();

        try {

            /*
             * Find the asset
             */
            $asset = $this->asset->find($this->getInput('asset_id'));

            /*
             * Check if any files have been uploaded
             */
            $files = $this->getInput('files');

            if ($files) {

                $records = array();

                /*
                 * For each file, create the attachment record, and sync asset image pivot table
                 */
                foreach ($files as $file) {

                    $attributes = explode('|', $file);

                    $fileName = $attributes[0];
                    $fileOriginalName = $attributes[1];

                    /*
                     * Ex. files/assets/images/1/example.png
                     */
                    $movedFilePath = config('maintenance::site.paths.assets.images') . sprintf('%s/', $asset->id);

                    /*
                     * Move the file
                     */
                    Storage::move(config('maintenance::site.paths.temp') . $fileName, $movedFilePath . $fileName);

                    /*
                     * Set insert data
                     */
                    $insert = array(
                        'name' => $fileOriginalName,
                        'file_name' => $fileName,
                        'file_path' => $movedFilePath,
                        'user_id' => $this->sentry->getCurrentUserId()
                    );

                    /*
                     * Create the attachment record
                     */
                    $records[] = $this->attachment->setInput($insert)->create();

                    /*
                     * Attach the attachment record to the asset images
                     */
                    $asset->images()->attach($record);

                }

                $this->dbCommitTransaction();

                /*
                 *  Return attachment record on success
                 */
                return $records;

            }

            $this->dbRollbackTransaction();

            /*
             * No Files were detected to be uploaded, return false
             */
            return false;


        } catch (\Exception $e) {

            $this->dbRollbackTransaction();

            return false;
        }
    }

}