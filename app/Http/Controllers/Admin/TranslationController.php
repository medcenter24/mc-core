<?php
/**
 * Copyright (c) 2016. Blog-Tree.com
 *
 * @author A.Zagovorichev, <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;

use App\Helpers\TranslationManager\Manager;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;
use Zagovorichev\Laravel\Languages\Manager\RequestManager;


class TranslationController extends AdminController
{
    /** @var \Barryvdh\TranslationManager\Manager  */
    protected $manager;

    private $locales;
    
    public function __construct(Manager $manager)
    {
        parent::__construct();
        
        $this->manager = $manager;
        $this->locales = config('translation-manager.locales');
        
        view()->share('current_menu', '4');
    }

    public function index($group = null)
    {
        $locales = $this->loadLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=> trans('admin/translate.choose_group_btn')] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();

        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach($allTranslations as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
        }

         return view('admin.translation.list')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', action('Admin\TranslationController@postEdit', [$group]))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    public function view(Request $request)
    {
        $group = $request->get('group', false);
        $sub_group = $request->get('sub_group', false);

        if (!$group) {
            return redirect('admin/translation')->with('error', trans('admin/translate.choose_group'));
        }

        if ($sub_group) {
            return $this->index($group.'/'.$sub_group);
        }

        return $this->index($group);
    }

    protected function loadLocales()
    {
        //Set the default locale as the first one.
        $locales = Translation::groupBy('locale')->pluck('locale');
        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }
        $locales = array_merge($this->locales, [config('app.locale')], $locales);
        return array_unique($locales);
    }

    public function getText(Request $request, $group, $sub_group = null)
    {
        $name = $request->get('name');

        list($locale, $key) = explode('|', $name, 2);

        $translation = Translation::firstOrCreate([
            'locale' => $locale,
            'group' => $sub_group ? $group . "/" . $sub_group: $group,
            'key' => $key,
        ]);

        return ['text' => $translation->value];
    }

    public function postAdd(Request $request, $group, $sub_group = null)
    {
        $keys = explode("\n", $request->get('keys'));

        if ($sub_group) {
            $group = $group . "/" . $sub_group;
        }

        foreach($keys as $key){
            $key = trim($key);
            if($group && $key){
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit(Request $request, $group, $sub_group = null)
    {
        if(!in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $name = $request->get('name');
            $value = $request->get('value');

            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $sub_group ? $group . "/" . $sub_group: $group,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return ['text' => $translation->value, 'status' => $translation->status];
        }
        
        return array('status' => 'failure');
    }

    public function postDelete($group, $sub_group = null, $key)
    {
        if(!in_array($group, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled')) {
            Translation::where('group', $group)->where('key', $key)->delete();
            return ['status' => 'ok'];
        }

        return array('status' => 'failure');
    }

    public function postImport(Request $request)
    {
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);

        return redirect('admin/translation')->with('successPublish', trans('admin/translate.imported') . ' ' . $counter . '! ' . trans('admin/translate.refresh'));
    }

    public function postFind()
    {
        $numFound = $this->manager->findTranslations();
        return redirect('admin/translation')->with('successPublish', trans('admin/translate.done_searching') . ' ' . (int) $numFound);
    }

    public function postPublish($group, $sub_group = null)
    {
        if ($sub_group) {
            $this->manager->exportTranslations($group.'/'.$sub_group);
        } else {
            $this->manager->exportTranslations($group);
        }

        return ['status' => 'ok'];
    }
}
