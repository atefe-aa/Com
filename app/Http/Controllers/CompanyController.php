<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = 10;
            $companies = Company::paginate($perPage);

            return CompanyResource::collection($companies);
        } catch (Exception $e) {
            Log::info("Failed to fetch companies. " . $e->getMessage());
            return response()->json(['message' => __('messages.failed')], 500);

        }

    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @param StoreCompanyRequest $request
     * @return CompanyResource|JsonResponse
     */
    public function store(StoreCompanyRequest $request)
    {
        try {
            $company = Company::create([
                "name" => $request->get("name"),
                "email" => $request->get("email"),
                "website" => $request->get("website")
            ]);

            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to create company. " . $e->getMessage());
            return response()->json(['message' => __('messages.failed')], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = Company::find($id);
            if (!$company) {
                return response()->json(['message' => __('messages.notFound')], 404);
            }
            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to fetch company. " . $e->getMessage());
            return response()->json(['message' => __('messages.failed')], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $id)
    {
        try {
            $company = Company::find($id);
            if (!$company) {
                return response()->json(['message' => __('messages.notFound')], 404);
            }
            $updateData = [];
            $incomingData = $request->all();
            foreach ($incomingData as $key => $value) {
                if ($key === 'name' && empty($value)) {
                    $field = __('validation.attributes.' . $key);
                    return response()->json(['message' => __('messages.notEmpty', ['attribute' => $field])], 422);
                }
                $updateData[$key] = $value;
            }

            $company->update($updateData);

            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to update company. " . $e->getMessage());
            return response()->json(['message' => __('messages.failed')], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $companyIsUsed = Employee::where('company_id', $id)->count() > 0;
            if ($companyIsUsed) {
                return response()->json(['message' => __('messages.deletionRestricted')], 403);
            }
            if ($company->delete()) {
                return response()->json(['data' => __('messages.success')]);
            }

            return response()->json(['message' => __('messages.failed')]);
        } catch (Exception $e) {
            Log::info("Failed to delete company. " . $e->getMessage());
            return response()->json(['message' => __('messages.failed')], 500);
        }
    }
}
