<?php

/*
 * Copyright 2005 - 2022 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
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

namespace Core\TimePeriod\Infrastructure\API\FindTimePeriod;

use Core\Application\Common\UseCase\AbstractPresenter;
use Core\Application\Common\UseCase\PresenterInterface;
use Core\Infrastructure\Common\Presenter\PresenterFormatterInterface;
use Core\TimePeriod\Application\UseCase\FindTimePeriod\FindTimePeriodResponse;

class FindTimePeriodPresenter extends AbstractPresenter implements PresenterInterface
{
    /**
     * @param PresenterFormatterInterface $presenterFormatter
     */
    public function __construct(PresenterFormatterInterface $presenterFormatter)
    {
        parent::__construct($presenterFormatter);
    }

    /**
     * {@inheritDoc}
     *
     * @param FindTimePeriodResponse $data
     */
    public function present(mixed $data): void
    {
        parent::present([
            'id' => $data->id,
            'name' => $data->name,
            'alias' => $data->alias,
            'days' => $data->days,
            'templates' => $data->templates,
            'exceptions' => $data->exceptions,
        ]);
    }
}
