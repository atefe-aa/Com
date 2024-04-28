<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = 10;
            $employees = Employee::paginate($perPage);

            return EmployeeResource::collection($employees);
        } catch (Exception $e) {
            Log::info("Failed to fetch employees. " . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch employees. Please try again later'], 500);

        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = Employee::create([
                "first_name" => $request->input('firstName'),
                "last_name" => $request->input('lastName'),
                "company_id" => $request->input('companyId'),
                "email" => $request->input('email'),
                "phone" => $request->input('phone'),
            ]);

            return new EmployeeResource($employee);
        } catch (Exception $e) {
            Log::info("Failed to create employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to create employee. Please try again later'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = Employee::findOrFail($id);
            return new EmployeeResource($company);
        } catch (Exception $e) {
            Log::info("Failed to fetch employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch employee. Please try again later'], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->update([
                "first_name" => $request->input('firstName') ?: $employee->fisrt_name,
                "last_name" => $request->input('lastName') ?: $employee->last_name,
                "company_id" => $request->input('companyId') ?: $employee->company_id,
                "email" => $request->input('email'),
                "phone" => $request->input('phone'),
            ]);

            return new EmployeeResource($employee);
        } catch (Exception $e) {
            Log::info("Failed to update employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to update employee. Please try again later'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($employee->delete()) {
                return response()->json(['data' => 'Employee deleted successfully']);
            }

            return response()->json(['message' => 'Failed to delete employee.'], 500);
        } catch (Exception $e) {
            Log::info("Failed to delete employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete employee. Please try again later'], 500);
        }
    }
}
