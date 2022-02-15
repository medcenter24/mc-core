<?php

declare(strict_types=1);

namespace medcenter24\mcCore\App\Http\Controllers\Admin\Entity;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;

class AccidentStatusController extends AdminController
{
    /**
     * @param AccidentStatusService $accidentStatusService
     * @return Factory|View|Application
     * @throws InconsistentDataException
     */
    public function index(AccidentStatusService $accidentStatusService): Factory|View|Application
    {
        $this->getMenuService()->markCurrentMenu('7.20');
        $accidentStatuses = $accidentStatusService->search();
        return view('admin.entity.accident-status.index', compact('accidentStatuses'));
    }

    public function store(AccidentStatusService $accidentStatusService): Redirector|Application|RedirectResponse
    {
        $accidentStatusService->getNewStatus();
        $accidentStatusService->getClosedStatus();
        $accidentStatusService->getDoctorAssignedStatus();
        $accidentStatusService->getDoctorInProgressStatus();
        $accidentStatusService->getDoctorRejectedStatus();
        $accidentStatusService->getDoctorSentStatus();
        $accidentStatusService->getImportedStatus();
        $accidentStatusService->getReopenedStatus();
        return redirect('admin/entity/accident-status')
            ->with(['flash_message' => trans('content.created')]);
    }
}
