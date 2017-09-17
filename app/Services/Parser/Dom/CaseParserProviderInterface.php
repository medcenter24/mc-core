<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 02.05.2017
 * Time: 19:50
 */

namespace app\Services\Parser\Dom;


interface CaseParserProviderInterface extends DomParserProviderInterface
{
    public function getTitle();
    public function getPatientName();
    public function getDoctorName();
    public function getServices();
    public function getSurveis();
}
