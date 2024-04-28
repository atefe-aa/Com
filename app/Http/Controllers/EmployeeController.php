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
                "first_name" => $request->get('firstName'),
                "last_name" => $request->get('lastName'),
                "company_id" => $request->get('companyId'),
                "email" => $request->get('email'),
                "phone" => $request->get('phone'),
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
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Failed to fetch employee. Employee not found'], 404);
            }
            return new EmployeeResource($employee);
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
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Failed to fetch employee. Employee not found'], 404);
            }
            $updateData = [];
            $incomingData = $request->all();
            foreach ($incomingData as $key => $value) {

                if (($key === 'firstName' || $key === 'lastName' || $key === 'companyId') && empty($value)) {
                    $field = __('validation.attributes.' . $key);
                    return response()->json(['message' => __('messages.notEmpty', ['attribute' => $field])], 422);
                }
                $column = $this->getColumn($key);
                $updateData[$column] = $value;
            }

            $employee->update($updateData);

            return new EmployeeResource($employee);
        } catch (Exception $e) {
            Log::info("Failed to update employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to update employee. Please try again later'], 500);
        }
    }

    private function getColumn(string $field)
    {
        return match ($field) {
            'firstName' => "first_name",
            'lastName' => "last_name",
            'companyId' => "company_id",
            default => $field,
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($employee->delete()) {
                return response()->json(['data' => __('messages.success')]);
            }

            return response()->json(['message' => 'Failed to delete employee.'], 500);
        } catch (Exception $e) {
            Log::info("Failed to delete employee. " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete employee. Please try again later'], 500);
        }
    }
}
