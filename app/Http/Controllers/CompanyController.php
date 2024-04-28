<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
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
            return response()->json(['message' => 'Failed to fetch companies. Please try again later'], 500);

        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        Log::info($request->all());
        try {
            $company = Company::create([
                "name" => $request->get("name"),
                "email" => $request->get("email"),
                "website" => $request->get("website")
            ]);

            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to create company. " . $e->getMessage());
            return response()->json(['message' => 'Failed to create company. Please try again later'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = Company::findOrFail($id);
            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to fetch company. " . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch company. Please try again later'], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->update([
                'name' => $request->input('name') ?: $company->name,
                'email' => $request->input('email'),
                'website' => $request->input('website')
            ]);

            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::info("Failed to update company. " . $e->getMessage());
            return response()->json(['message' => 'Failed to update company. Please try again later'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = Company::findOrFail($id);
            if ($company->delete()) {
                return response()->json(['data' => 'Company deleted successfully']);
            }

            return response()->json(['message' => 'Failed to delete company.']);
        } catch (Exception $e) {
            Log::info("Failed to delete company. " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete company. Please try again later'], 500);
        }
    }
}
