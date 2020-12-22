<?php

namespace ch\makae\makaegallery;

class UploadResult implements \JsonSerializable
{

    private array $results = [];
    private bool $success = true;

    public function addFileResult(UploadStatus $result)
    {
        $this->results[] = $result;

        if (!$result->isSuccess()) {
            $this->success = false;
        }
    }

    public function isSuccess()
    {
        return $this->success;
    }


    public function jsonSerialize(): array
    {
        return [
            'results' => $this->results,
            'success' => $this->success
        ];
    }

}
