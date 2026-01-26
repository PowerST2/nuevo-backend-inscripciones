<?php
namespace App\Traits;

use App\Models\Ubigeo;

trait UbigeoTrait
{
    /**
     * 1. Listar Departamentos
     * Filtramos los códigos que terminan en '0000' (Son las cabeceras de departamento)
     */
    public function getDepartments()
    {
        return Ubigeo::where('code', 'LIKE', '%0000')
            ->select('code', 'department as name')
            ->orderBy('department', 'asc')
            ->get(); 
            // Retorna: [{code: "010000", department: "AMAZONAS"}, ...]
    }

    /**
     * 2. Listar Provincias por Código de Departamento
     * Recibe: "150000" (Código de Lima Dpto)
     * Lógica: 
     * - Debe empezar con los primeros 2 dígitos del dpto ('15%').
     * - Debe terminar en '00' (Es cabecera de provincia).
     * - NO debe ser el código del dpto original ('150000').
     */
    public function getProvincesByDepartmentCode(string $departmentCode)
    {
        // Extraemos los 2 primeros dígitos (ej: "15")
        $prefix = substr($departmentCode, 0, 2);

        return Ubigeo::where('code', 'LIKE', $prefix . '%00') // Empieza con 15, termina en 00
            ->where('code', '!=', $departmentCode)            // Excluye 150000
            ->select('code', 'province as name')
            ->orderBy('province', 'asc')
            ->get();
            // Retorna: [{code: "150100", province: "LIMA"}, {code: "150200", province: "BARRANCA"}...]
    }

    /**
     * 3. Listar Distritos por Código de Provincia
     * Recibe: "150100" (Código de Lima Prov)
     * Lógica:
     * - Debe empezar con los primeros 4 dígitos de la prov ('1501%').
     * - NO debe terminar en '00' (Para que sea distrito real).
     */
    public function getDistrictsByProvinceCode(string $provinceCode)
    {
        // Extraemos los 4 primeros dígitos (ej: "1501")
        $prefix = substr($provinceCode, 0, 4);

        return Ubigeo::where('code', 'LIKE', $prefix . '%')   // Empieza con 1501
            ->where('code', 'NOT LIKE', '%00')                // No termina en 00 (Es distrito)
            ->select('id','district as name')
            ->orderBy('district', 'asc')
            ->get();
            // Retorna: [{id: 123, code: "150101", district: "LIMA", ...}, ...]
    }

    /**
     * Obtener por ID (Para cargar datos)
     */
    public function getUbigeoById(int $id)
    {
        return Ubigeo::find($id);
    }
}