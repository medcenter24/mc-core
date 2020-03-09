<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Entity\Form;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\File\TmpFileService;
use medcenter24\mcCore\App\Services\Form\FormVariableService;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

class FormService extends AbstractModelService
{
    use ServiceLocatorTrait;

    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_TEMPLATE = 'template';
    public const FIELD_FORMABLE_TYPE = 'formable_type';
    public const FIELD_VARIABLES = 'variables';

    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_TEMPLATE,
        self::FIELD_FORMABLE_TYPE,
        self::FIELD_VARIABLES,
    ];
    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_TEMPLATE,
        self::FIELD_FORMABLE_TYPE,
        self::FIELD_VARIABLES,
    ];
    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_TEMPLATE,
        self::FIELD_FORMABLE_TYPE,
        self::FIELD_VARIABLES,
    ];

    /**
     * Filesystem constants
     */
    public const PDF_DISK = 'pdfForms';
    public const PDF_FOLDER = 'pdfForms';

    /**
     * Example
     * `<div :template.if=":variable.name">content</div>`
     *
     * Content will be shown only when if is true (nor '' and nor 0)
     */
    public const CONDITION_IF = ':template.if';
    /**
     * Example
     * `<div :template.for=":accident.services" class="services">
     *     <div>:template.for.resource.id<div>
     *      :template.for.element.title
     * </div>`
     *
     * As a content will be list of services (only if exists with repeated body):
     * `<div class="services">
     *     <div>1</div>
     *      service 1 title
     *     <div>2</div>
     *      service 2 title
     * </div>`
     */
    public const CONDITION_FOR = ':template.for';
    public const CONDITION_FOR_RESOURCE = ':template.for.resource';

    /**
     * @param Form $form
     * @param Model $source
     * @return string
     * @throws InconsistentDataException
     */
    public function getPdfPath(Form $form, Model $source): string
    {
        $uniq = $this->getCacheableUniqValue($source) . '.pdf';

        if (!Storage::disk(self::PDF_DISK)->exists($uniq)) {
            $this->toPdf($form, $source, $uniq);
        }

        return Storage::disk(self::PDF_DISK)->path($uniq);
    }

    /**
     * @param Form $form
     * @param Model $source
     * @param string $path
     * @throws InconsistentDataException
     */
    public function toPdf(Form $form, Model $source, $path = 'file.pdf'): void
    {
        /** @var TmpFileService $tmpFileService */
        $tmpFileService = $this->getServiceLocator()->get(TmpFileService::class);
        try {
            $mpdf = new Mpdf([
                'tempDir' => $tmpFileService->getStoragePath(),
                'debug' => true,
                'useSubstitutions' => false,
                'simpleTables' => true,
                'use_kwt' => true,
                'shrink_tables_to_fit' => 1,
                'showImageErrors' => true,
                /*'allowCJKorphans' => false,
                'allowCJKoverflow' => true,*/
                /*'ignore_table_percents' => true,
                'ignore_table_widths' => true,*/
                /*'keepColumns' => true,
                'keep_table_proportions' => true,*/
                // 'justifyB4br' => true,
                'margin_left' => 3,
                'margin_right' => 5,
                'margin_top' => 9,
                'margin_bottom' => 1,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            $mpdf->WriteHTML($this->getHtml($form, $source));

            $mpdf->SetTitle('Form ' . $form->title . ' (' . $source->id . ')');
            $mpdf->SetAuthor(config('name'));
            /*$mpdf->SetWatermarkText("Paid");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;*/

            /*if ($this->report->hasDocuments()) {
                $mpdf->AddPage();
                $mpdf->WriteHTML($this->htmlDocuments());
            }*/
            $mpdf->SetDisplayMode('fullpage');

            $mpdf->Output(Storage::disk(self::PDF_DISK)->path($path), Destination::FILE);
        } catch (MpdfException $e) {
            Log::debug($e->getMessage());
        }
    }

    /**
     * The value which perform state of the statistic to be cached and do not generated all the times
     * @param Model $model
     * @return string
     * @throws InconsistentDataException
     */
    private function getCacheableUniqValue(Model $model): string
    {
        $modelClass = get_class($model);
        switch ($modelClass) {
            case Accident::class :
                $value = $model->ref_num . '_' . $model->updated_at->format('Ymd_His');
                break;
            default:
                throw new InconsistentDataException('Undefined model ' . $modelClass);
        }
        return $value;
    }

    /**
     * @param Form $form
     * @param Model $source
     * @return string
     * @throws InconsistentDataException
     */
    public function getHtml(Form $form, Model $source): string
    {
        /** @var FormVariableService $formVariableService */
        $this->checkModel($form, $source);
        $template = $form->getAttribute('template');
        $template = '<html><body id="form-body">' . $template . '</body></html>';
        $template = $this->applyConditions($template, $source);
        $template = $this->applyVariables($template, $source);
        $template = Str::replaceFirst('<html><body id="form-body">', '', $template);
        $template = Str::replaceLast('</body></html>', '', $template);
        return $template;
    }

    private function applyConditions(string $template, Model $source): string
    {
        // exclude errors with tags that can be auto fixed
        libxml_use_internal_errors(true);
        $template = $this->applyIfConditions($template, $source);
        $template = $this->applyForConditions($template, $source);
        return $template;
    }

    /**
     * @param string $template
     * @param Model $source
     * @return string
     * @throws InconsistentDataException
     */
    private function applyIfConditions(string $template, Model $source): string
    {
        $doc = $this->getDom($template);
        /** @var DOMElement $el */
        foreach ($this->findDomElements('starts-with(name(@*),"'.self::CONDITION_IF.'")', $doc) as $el) {
            $attr = $el->getAttribute(self::CONDITION_IF);
            $attrVal = $this->getAccidentValue($source, $attr);
            // if should not be shown
            if (!$attrVal || empty($attrVal) || $attrVal === '0') {
                $el->parentNode->removeChild($el);
            } else {
                $el->removeAttribute(self::CONDITION_IF);
            }
        }
        $template = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
        $template = str_replace('  ', ' ', $template);
        return $template;
    }

    private function getDom(string $template = ''): DOMDocument
    {
        $dom = new DOMDocument();
        $template = mb_convert_encoding($template, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($template, LIBXML_NOBLANKS|LIBXML_HTML_NOIMPLIED);
        return $dom;
    }

    private function findDomElements(string $search, DOMDocument $dom): DOMNodeList
    {
        $selector = new DOMXPath($dom);
        return $selector->query('//*['.$search.']');
    }

    /**
     * For condition produces with top html element
     * @example `<div :template.for>content of :template.for.resource.id<div>`
     *      if we have 2 records:
     *          `<div>content of 1</div>
     *           <div>content of 2</div>`
     * @param string $template
     * @param Model $source
     * @return string
     */
    private function applyForConditions(string $template, Model $source): string
    {
        $doc = $this->getDom($template);
        /** @var DOMElement $el */
        foreach ($this->findDomElements('starts-with(name(@*),"'.self::CONDITION_FOR.'")', $doc) as $el) {
            $attr = $el->getAttribute(self::CONDITION_FOR);
            try {
                $attrResources = $this->getAccidentResources($source, $attr);
            } catch (InconsistentDataException $e) {
                $attrResources = null;
            }
            if (!$attrResources || !$attrResources->count()) {
                // no results - delete full element
                $el->parentNode->removeChild($el);
            } else {
                // has results - delete attribute only
                $el->removeAttribute(self::CONDITION_FOR);

                // we need to duplicate this content for each entity of the resources
                // that save the text only
                // $bodyContent = $el->textContent;
                // that save html of the element
                $bodyContent = $el->ownerDocument->saveHTML($el);

                $newBody = '';

                // and fill content body
                $attrResources->map(static function (Model $val) use (&$newBody, $bodyContent) {
                    $part = $bodyContent;
                    foreach ($val->getVisible() as $param) {
                        $part = str_replace(FormService::CONDITION_FOR_RESOURCE . '.' . $param,
                            $val->getAttribute($param),
                            $part);
                    }
                    $newBody .= $part;
                });

                // I need to create core node for the body
                $newBody = '<span '.self::CONDITION_FOR.'="marked_for_selector">'.$newBody.'</span>';
                $newDom = $this->getDom($newBody);
                $elements = $this->findDomElements('starts-with(name(@*),"'.self::CONDITION_FOR.'")', $newDom);
                /** @var DOMElement $newElement */
                $newElement = $elements->item(0);
                $newElement->removeAttribute(self::CONDITION_FOR);
                // $newElement->removeAttribute(self::CONDITION_FOR);
                if ($importedNode = $el->ownerDocument->importNode($newElement, true) ) {
                    $el->parentNode->replaceChild($importedNode, $el);
                }
            }
        }
        $template = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
        $template = str_replace('  ', ' ', $template);
        return $template;
    }

    private function applyVariables(string $template, Model $source): string
    {
        foreach ($this->getFormVariableService()->getAccidentVariables() as $map) {
            $value = $this->getAccidentValue($source, $map);
            $value = $value ?: 'VARIABLE_STILL_NOT_SET_'.$map;
            $template = str_replace($map, $value, $template);
        }
        return $template;
    }

    /**
     * @return FormVariableService
     */
    private function getFormVariableService(): FormVariableService
    {
        return $this->getServiceLocator()->get(FormVariableService::class);
    }

    /**
     * @param Model $accident
     * @param string $var
     * @return string
     * @throws InconsistentDataException
     */
    private function getAccidentValue(Model $accident, string $var): string
    {
        $map = trim($var, ':');
        $map = explode('.', $map);
        $obj = $accident;
        array_shift($map); // pop accident
        $val = '';
        $date = null;
        $dateFormat = '';
        foreach ($map as $property) {
            if ( isset($obj->$property) ) {
                if (is_object($obj->$property)) {
                    if ($obj->$property instanceof Carbon) {
                        /** @var Carbon $date */
                        $date = $obj->$property;
                    } else {
                        $obj = $obj->$property;
                    }
                } else {
                    $val = $obj->$property;
                }
            } elseif ($date) {
                $dateFormat .= $property;
            }
        }

        if ($date) {
            $val = $this->getFormattedDate($dateFormat, $date);
        }
        return (string)$val;
    }

    /**
     * @param Model $accident
     * @param string $var
     * @return Collection
     * @throws InconsistentDataException
     */
    private function getAccidentResources(Model $accident, string $var): Collection
    {
        $map = trim($var, ':');
        $map = explode('.', $map);
        $obj = $accident;
        array_shift($map); // pop accident
        foreach ($map as $property) {
            if ( isset($obj->$property) && is_object($obj->$property)) {
                $obj = $obj->$property;
            }
        }
        if (!is_a($obj, Collection::class)) {
            throw new InconsistentDataException('The map is not found in the model');
        }
        return $obj;
    }

    /**
     * @param Form $form
     * @param Model $model
     * @throws InconsistentDataException
     */
    private function checkModel(Form $form, Model $model): void
    {
        if ($form->getAttribute('formable_type') !== get_class($model)) {
            throw new InconsistentDataException('This model not supported by this form');
        }
    }

    /**
     * @param string $format
     * @param Carbon $date
     * @return string
     * @throws InconsistentDataException
     */
    private function getFormattedDate(string $format, Carbon $date): string
    {
        if (empty($format)) {
            $format = $date->format(config('date.actionFormat'));
        } else {
            // convert date to the val format
            switch ($format) {
                case 'time':
                    $format = $date->format(config('date.timeFormat'));
                    break;
                case 'date':
                    $format = $date->format(config('date.dateFormat'));
                    break;
                default:
                    throw new InconsistentDataException('Undefined date format');
            }
        }

        return $format;
    }

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Form::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_DESCRIPTION => '',
            self::FIELD_TEMPLATE => '',
            self::FIELD_FORMABLE_TYPE => '',
            self::FIELD_VARIABLES => [],
        ];
    }
}
