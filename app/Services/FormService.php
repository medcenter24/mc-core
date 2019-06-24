<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Form;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Form\FormVariableService;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class FormService
{
    use ServiceLocatorTrait;
    /**
     * Filesystem constants
     */
    public const PDF_DISK = 'pdfForms';
    public const PDF_FOLDER = 'pdfForms';

    /**
     * @param Form $form
     * @param Model $source
     * @return string
     * @throws InconsistentDataException
     */
    public function getPdfPath(Form $form, Model $source): string
    {
        $uniq = $this->getCacheableUniqValue($source) . '.pdf';

        if (!\Storage::disk(self::PDF_DISK)->exists($uniq)) {
            $this->toPdf($form, $source, $uniq);
        }

        return \Storage::disk(self::PDF_DISK)->path($uniq);
    }

    public function toPdf(Form $form, Model $source, $path = 'file.pdf'): void
    {
        try {
            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
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

            $mpdf->Output(\Storage::disk(self::PDF_DISK)->path($path), Destination::FILE);
        } catch (\Mpdf\MpdfException $e) {
            \Log::debug($e->getMessage());
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
        $formVariableService = $this->getServiceLocator()->get(FormVariableService::class);
        $this->checkModel($form, $source);
        $template = $form->template;
        foreach ($formVariableService->getAccidentVariables() as $map) {
            $value = $this->getAccidentValue($source, $map);
            $value = $value ?: 'VARIABLE_STILL_NOT_SET';
            $template = str_replace($map, $value, $template);
        }
        return $template;
    }

    private function getAccidentValue(Model $accident, string $var): string
    {
        $map = trim($var, ':');
        $map = explode('.', $map);
        $obj = $accident;
        array_shift($map); // pop accident
        $val = '';
        foreach ($map as $property) {
            if ( isset($obj->$property) ) {
                if (is_object($obj->$property)) {
                    $obj = $obj->$property;
                } else {
                    $val = $obj->$property;
                }
            }
        }
        return (string)$val;
    }

    /**
     * @param Form $form
     * @param Model $model
     * @throws InconsistentDataException
     */
    private function checkModel(Form $form, Model $model): void
    {
        if ($form->formable_type !== get_class($model)) {
            throw new InconsistentDataException('This model not supported by this form');
        }
    }
}
