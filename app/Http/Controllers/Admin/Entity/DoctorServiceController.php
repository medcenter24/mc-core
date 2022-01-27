<?php

declare(strict_types=1);

namespace medcenter24\mcCore\App\Http\Controllers\Admin\Entity;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Services\Entity\ServiceService;

class DoctorServiceController extends AdminController
{
    /**
     * @param ServiceService $serviceService
     * @return Factory|View|Application
     * @throws InconsistentDataException
     */
    public function index(ServiceService $serviceService): Factory|View|Application
    {
        $this->getMenuService()->markCurrentMenu('7.10');
        $services = $serviceService->search();
        return view('admin.entity.doctor-service.index', compact('services'));
    }
}
