<?php

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Unit\Services;

use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\LogoService;
use PHPUnit\Framework\TestCase;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

class LogoServiceTest extends TestCase
{
    private LogoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LogoService();
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function testSetLogo(): void
    {
        $file = 'File';

        $adderMock = $this->createMock(FileAdder::class);
        $adderMock->expects($this->once())->method('toMediaCollection')
            ->with(LogoService::FOLDER, LogoService::DISC);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())->method('addMedia')
            ->with($file)
            ->willReturn($adderMock);
        $this->service->setLogo($userMock, $file);
    }

    public function testCheckDirectorAccess(): void
    {
        $roleServiceMock = $this->createMock(RoleService::class);
        $roleServiceMock->expects($this->once())
            ->method('hasRole')
            ->willReturn(true);

        $userMock = $this->createMock(User::class);
        $user2Mock = $this->createMock(User::class);

        $this->assertTrue($this->service->checkAccess($userMock, $user2Mock, $roleServiceMock));
    }

    public function testCheckDoctorAccess(): void
    {
        $roleServiceMock = $this->createMock(RoleService::class);
        $roleServiceMock->expects($this->exactly(2))
            ->method('hasRole')
            ->willReturnCallback(static function($user, $role) {
                return $role === RoleService::DOCTOR_ROLE;
            });

        $userMock = $this->createMock(User::class);
        $userMock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn(1);
        $user2Mock = $this->createMock(User::class);
        $user2Mock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn(1);

        $this->assertTrue($this->service->checkAccess($userMock, $user2Mock, $roleServiceMock));
    }

    public function testWrongDoctorDenied(): void
    {
        $roleServiceMock = $this->createMock(RoleService::class);
        $roleServiceMock->expects($this->exactly(2))
            ->method('hasRole')
            ->willReturnCallback(static function($user, $role) {
                return $role === RoleService::DOCTOR_ROLE;
            });

        $userMock = $this->createMock(User::class);
        $userMock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn(1);
        $user2Mock = $this->createMock(User::class);
        $user2Mock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn(2);

        $this->assertFalse($this->service->checkAccess($userMock, $user2Mock, $roleServiceMock));
    }

    public function testWrongRoleDenied(): void
    {
        $roleServiceMock = $this->createMock(RoleService::class);
        $roleServiceMock->expects($this->exactly(2))
            ->method('hasRole')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);
        $user2Mock = $this->createMock(User::class);

        $this->assertFalse($this->service->checkAccess($userMock, $user2Mock, $roleServiceMock));
    }
}
