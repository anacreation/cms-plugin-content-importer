<?php
/**
 * Author: Xavier Au
 * Date: 2019-02-04
 * Time: 16:57
 */

namespace Anacreation\CmsContentImporter\Entities;


use Anacreation\Cms\Contracts\ContentGroupInterface;
use Anacreation\Cms\Entities\ContentObject;

class ImportContentDTO
{

    private $owner;
    private $contentObject;

    /**
     * @return mixed
     */
    public function getOwner(): ContentGroupInterface {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner(ContentGroupInterface $owner): ImportContentDTO {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentObject(): ContentObject {
        return $this->contentObject;
    }

    /**
     * @param mixed $contentObject
     */
    public function setContentObject(ContentObject $contentObject
    ): ImportContentDTO {
        $this->contentObject = $contentObject;

        return $this;
    }
}