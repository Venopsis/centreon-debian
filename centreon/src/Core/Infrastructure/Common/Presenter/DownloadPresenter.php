<?php

/*
 * Copyright 2005 - 2022 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 *
 */

declare(strict_types=1);

namespace Core\Infrastructure\Common\Presenter;

use Core\Application\Common\UseCase\AbstractPresenter;

class DownloadPresenter extends AbstractPresenter implements DownloadInterface
{
    private const CSV_FILE_EXTENSION = 'csv';
    private const JSON_FILE_EXTENSION = 'json';

    private string $downloadFileName = '';

    /**
     * @inheritDoc
     */
    public function present(mixed $data): void
    {
        $originalHeaders = $this->getResponseHeaders();
        $originalHeaders['Content-Type'] = 'application/force-download';
        $originalHeaders['Content-Disposition'] = 'attachment; filename="' . $this->generateDownloadFileName() . '"';
        $this->setResponseHeaders($originalHeaders);
        parent::present($data);
    }

    /**
     * @param string $fileName
     * @return void
     */
    public function setDownloadFileName(string $fileName): void
    {
        $this->downloadFileName = $fileName;
    }

    /**
     * Generates download file extension depending on presenter
     *
     * @return string
     */
    private function generateDownloadFileExtension(): string
    {
        return match (get_class($this->presenterFormatter)) {
            CsvFormatter::class => self::CSV_FILE_EXTENSION,
            JsonFormatter::class => self::JSON_FILE_EXTENSION,
            default => '',
        };
    }

    /**
     * Generates download file name (name + extension depending on used presenter)
     *
     * @return string
     */
    private function generateDownloadFileName(): string
    {
        $fileExtension = $this->generateDownloadFileExtension();
        if ($fileExtension === '') {
            return $this->downloadFileName;
        }

        return $this->downloadFileName . '.' . $fileExtension;
    }
}
