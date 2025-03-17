<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{
  /**
 * @OA\Post(
 *     path="/v1/permissions",
 *     tags={"Role & Permission"},
 *     summary="Create permissions",
 *     description="Creates one or more permissions. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="name", type="string", example="view_users"),
 *                 @OA\Property(property="guard_name", type="string", example="web", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Permissions created successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=201),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permissions created successfully.")
 *             ),
 *             @OA\Property(property="result", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="view_users"),
 *                     @OA\Property(property="guard_name", type="string", example="web"),
 *                     @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred."))
 *         )
 *     )
 * )
 */
public function createPermissions(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            '*.name' => 'required|string|max:255|unique:permissions,name',
            '*.guard_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => ['code' => 422, 'success' => false, 'message' => 'Validation error.'],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $permissions = [];
        foreach ($request->all() as $permissionData) {
            $permissions[] = Permission::create($permissionData);
        }

        // Log activity
        activity('Permission')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'permissions' => $permissions,
                'description' => 'Created permissions.',
            ])
            ->log('Permissions Created');

        return response()->json([
            'meta' => ['code' => 201, 'success' => true, 'message' => 'Permissions created successfully.'],
            'result' => $permissions,
            'errors' => [],
        ], 201);
    } catch (\Exception $e) {
        Log::error('Create Permissions Error: ' . $e->getMessage());
        return response()->json([
            'meta' => ['code' => 500, 'success' => false, 'message' => 'Server error.'],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Post(
 *     path="/v1/roles",
 *     tags={"Role & Permission"},
 *     summary="Create roles",
 *     description="Creates one or more roles and assigns permissions to each role. At least one permission is required per role. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 required={"name", "permissions"},
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="string", example="view_users")
 *                 ),
 *                 @OA\Property(property="guard_name", type="string", example="web", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Roles created successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=201),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Roles created successfully.")
 *             ),
 *             @OA\Property(property="result", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin"),
 *                     @OA\Property(property="guard_name", type="string", example="web"),
 *                     @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z"),
 *                     @OA\Property(property="permissions", type="array",
 *                         @OA\Items(type="string", example="view_users")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
 *                 @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="The permissions field is required."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function createRoles(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            '*.name' => 'required|string|max:255|unique:roles,name',
            '*.permissions' => 'required|array|min:1',
            '*.permissions.*' => 'string|exists:permissions,name',
            '*.guard_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => ['code' => 422, 'success' => false, 'message' => 'Validation error.'],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $roles = [];
        foreach ($request->all() as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::create($roleData);
            $role->syncPermissions($permissions);
            $roles[] = $role->load('permissions');
        }

        // Log activity
        activity('Role')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'roles' => $roles,
                'description' => 'Created roles with permissions.',
            ])
            ->log('Roles Created');

        return response()->json([
            'meta' => ['code' => 201, 'success' => true, 'message' => 'Roles created successfully.'],
            'result' => $roles,
            'errors' => [],
        ], 201);
    } catch (\Exception $e) {
        Log::error('Create Roles Error: ' . $e->getMessage());
        return response()->json([
            'meta' => ['code' => 500, 'success' => false, 'message' => 'Server error.'],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Post(
 *     path="/v1/roles/{id}/permissions",
 *     tags={"Role & Permission"},
 *     summary="Assign permissions to a role",
 *     description="Assign one or more permissions to an existing role. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the role to assign permissions to.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="permissions", type="array",
 *                 @OA\Items(type="string", example="view_users")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permissions assigned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permissions assigned successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="string", example="view_users")
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="The specified role does not exist.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="The permissions field is required."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function assignPermissionsToRole(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => ['code' => 422, 'success' => false, 'message' => 'Validation error.'],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'meta' => ['code' => 404, 'success' => false, 'message' => 'Role not found.'],
                'result' => [],
                'errors' => ['The specified role does not exist.'],
            ], 404);
        }

        $role->syncPermissions($request->permissions);

        // Log activity
        activity('Role')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'role_id' => $role->id,
                'permissions' => $request->permissions,
                'description' => 'Assigned permissions to role',
            ])
            ->log('Permissions Assigned to Role');

        return response()->json([
            'meta' => ['code' => 200, 'success' => true, 'message' => 'Permissions assigned successfully.'],
            'result' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Assign Permissions to Role Error: ' . $e->getMessage());
        return response()->json([
            'meta' => ['code' => 500, 'success' => false, 'message' => 'Server error.'],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Get(
 *     path="/v1/roles",
 *     tags={"Role & Permission"},
 *     summary="Get all roles with permissions",
 *     description="Retrieve all roles along with their assigned permissions. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Roles retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Roles retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin"),
 *                     @OA\Property(property="permissions", type="array",
 *                         @OA\Items(type="string", example="view_users")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getAllRoles(Request $request)
{
    try {
        $roles = Role::with('permissions')->get();

        // Log activity
        activity('Role')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Retrieved all roles with permissions',
                'role_count' => $roles->count(),
            ])
            ->log('Roles Retrieved');

        return response()->json([
            'meta' => ['code' => 200, 'success' => true, 'message' => 'Roles retrieved successfully.'],
            'result' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name'),
                ];
            }),
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get All Roles Error: ' . $e->getMessage());
        return response()->json([
            'meta' => ['code' => 500, 'success' => false, 'message' => 'Server error.'],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Get(
 *     path="/v1/roles/{id}",
 *     tags={"Role & Permission"},
 *     summary="Get role details by ID",
 *     description="Fetch the details of a specific role, including its associated permissions. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the role to fetch details",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role details retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Role details retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="string", example="create-user")
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Role does not exist."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getRoleById($id, Request $request)
{
    try {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            // Log activity for not found
            activity('Roles')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to retrieve role with ID: $id",
                ])
                ->log('Role Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => ['Role does not exist.'],
            ], 404);
        }

        // Log activity for success
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'role_details' => $role,
                'description' => "Retrieved role with ID: $id",
            ])
            ->log('Role Retrieved');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Role details retrieved successfully.',
            ],
            'result' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get Role By ID Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Delete(
 *     path="/v1/roles/{id}",
 *     tags={"Role & Permission"},
 *     summary="Delete a role",
 *     description="Deletes a specific role by ID. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the role to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Role deleted successfully.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Role does not exist."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function deleteRole($id, Request $request)
{
    try {
        $role = Role::find($id);

        if (!$role) {
            // Log activity for not found
            activity('Roles')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to delete role with ID: $id",
                ])
                ->log('Role Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => ['Role does not exist.'],
            ], 404);
        }

        // Delete the role
        $role->delete();

        // Log activity for success
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'role_id' => $id,
                'description' => 'Deleted role with ID: ' . $id,
            ])
            ->log('Role Deleted');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Role deleted successfully.',
            ],
            'result' => [],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Delete Role Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Get(
 *     path="/v1/permissions",
 *     tags={"Role & Permission"},
 *     summary="Get all permissions",
 *     description="Retrieve a list of all available permissions. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Permissions retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permissions retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="edit articles"),
 *                     @OA\Property(property="guard_name", type="string", example="web"),
 *                     @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getAllPermissions(Request $request)
{
    try {
        $permissions = Permission::all();

        // Log activity
        activity('Permissions')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Retrieved all permissions.',
                'permissions_count' => $permissions->count(),
            ])
            ->log('Permissions Retrieved');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permissions retrieved successfully.',
            ],
            'result' => $permissions,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get All Permissions Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Delete(
 *     path="/v1/roles/{role_id}/permissions",
 *     tags={"Role & Permission"},
 *     summary="Detach permissions from a role",
 *     description="Detach one or more permissions from a specific role. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="role_id",
 *         in="path",
 *         required=true,
 *         description="The ID of the role from which permissions will be detached.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="permissions", type="array", description="Array of permission IDs to detach",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permissions detached successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permissions detached successfully.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role or permissions not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role or permissions not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function detachPermissionsFromRole(Request $request, $role_id)
{
    try {
        // Validate input
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'success' => false,
                    'message' => 'Validation error.',
                ],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::find($role_id);

        if (!$role) {
            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        $permissions = Permission::whereIn('id', $request->permissions)->get();

        if ($permissions->isEmpty()) {
            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Permissions not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        $role->revokePermissionTo($permissions);

        // Log activity
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Detached permissions from role.',
                'role' => $role->name,
                'detached_permissions' => $permissions->pluck('name')->toArray(),
            ])
            ->log('Permissions Detached');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permissions detached successfully.',
            ],
            'result' => [],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Detach Permissions From Role Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}

/**
 * @OA\Delete(
 *     path="/v1/permissions/{id}",
 *     tags={"Role & Permission"},
 *     summary="Delete a specific permission",
 *     description="Deletes a specific permission by its ID. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The ID of the permission to delete.",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permission deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permission deleted successfully.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Permission not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Permission not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function deletePermission($id, Request $request)
{
    try {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Permission not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        // Get permission details for logging
        $permissionDetails = $permission->toArray();

        $permission->delete();

        // Log activity
        activity('Permissions')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Deleted a permission.',
                'permission_details' => $permissionDetails,
            ])
            ->log('Permission Deleted');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permission deleted successfully.',
            ],
            'result' => [],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Delete Permission Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Delete(
 *     path="/v1/permissions",
 *     tags={"Role & Permission"},
 *     summary="Delete multiple permissions",
 *     description="Deletes multiple permissions by their IDs. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="ids", type="array", description="Array of permission IDs to delete",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permissions deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permissions deleted successfully.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="One or more permissions not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="One or more permissions not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error: No IDs provided.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="No IDs provided.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function deletePermissions(Request $request)
{
    try {
        $ids = $request->input('ids', []);

        // Validate the input
        if (empty($ids)) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'success' => false,
                    'message' => 'No IDs provided.',
                ],
                'result' => [],
                'errors' => [],
            ], 422);
        }

        // Fetch the permissions
        $permissions = Permission::whereIn('id', $ids)->get();

        if ($permissions->isEmpty()) {
            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'One or more permissions not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        // Collect details for logging
        $permissionDetails = $permissions->toArray();

        // Delete permissions
        Permission::whereIn('id', $ids)->delete();

        // Log activity
        activity('Permissions')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Deleted multiple permissions.',
                'permission_details' => $permissionDetails,
            ])
            ->log('Permissions Deleted');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permissions deleted successfully.',
            ],
            'result' => [],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Delete Permissions Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}

/**
 * @OA\Get(
 *     path="/v1/permissions/{id}",
 *     tags={"Role & Permission"},
 *     summary="Get a specific permission",
 *     description="Retrieve the details of a specific permission by its ID. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the permission to retrieve",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permission retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permission retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="edit-post"),
 *                 @OA\Property(property="guard_name", type="string", example="web"),
 *                 @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Permission not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Permission not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getPermissionById($id, Request $request)
{
    try {
        $permission = Permission::find($id);

        if (!$permission) {
            // Log activity for not found
            activity('Permissions')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to retrieve permission with ID: $id",
                ])
                ->log('Permission Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Permission not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        // Log activity for success
        activity('Permissions')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'permission' => $permission,
                'description' => "Retrieved permission with ID: $id",
            ])
            ->log('Permission Retrieved');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permission retrieved successfully.',
            ],
            'result' => $permission,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get Permission By ID Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Put(
 *     path="/v1/permissions/{id}",
 *     tags={"Role & Permission"},
 *     summary="Edit a specific permission",
 *     description="Updates the details of a specific permission by its ID. Only the name of the permission can be updated. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the permission to edit",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="edit-post")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Permission updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Permission updated successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="edit-post"),
 *                 @OA\Property(property="guard_name", type="string", example="web"),
 *                 @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Permission not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Permission not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An empty array.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="The name field is required.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Internal server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function updatePermission(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'success' => false,
                    'message' => 'Validation error.',
                ],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = Permission::find($id);

        if (!$permission) {
            // Log activity for not found
            activity('Permissions')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to find permission with ID: $id",
                ])
                ->log('Permission Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Permission not found.',
                ],
                'result' => [],
                'errors' => [],
            ], 404);
        }

        $permission->update(['name' => $request->name]);

        // Log activity for success
        activity('Permissions')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'permission_id' => $permission->id,
                'new_name' => $permission->name,
                'description' => "Updated permission name to: {$permission->name}",
            ])
            ->log('Permission Updated');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Permission updated successfully.',
            ],
            'result' => $permission,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Edit Permission Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Internal server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}




/**
 * @OA\Get(
 *     path="/v1/roles/with-permissions",
 *     tags={"Role & Permission"},
 *     summary="Get all roles with their permissions",
 *     description="Retrieve all roles and their associated permissions. If a role has no permissions assigned, it will still be included. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Roles retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Roles retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin"),
 *                     @OA\Property(property="permissions", type="array",
 *                         @OA\Items(type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="edit-post"),
 *                             @OA\Property(property="guard_name", type="string", example="web"),
 *                             @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                             @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *                         )
 *                     ),
 *                     @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *                 )
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Internal server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getRolesWithPermissions(Request $request)
{
    try {
        $roles = Role::with('permissions')->get();

        // Log activity
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'roles_count' => $roles->count(),
                'description' => 'Retrieved all roles with permissions',
            ])
            ->log('Roles Retrieved with Permissions');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Roles retrieved successfully.',
            ],
            'result' => $roles,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get Roles with Permissions Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Internal server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Get(
 *     path="/v1/roles/{id}/permissions",
 *     tags={"Role & Permission"},
 *     summary="Get a specific role with its permissions",
 *     description="Retrieve a specific role and its associated permissions by role ID. If the role has no permissions assigned, it will still be included. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the role to retrieve",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Role retrieved successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="edit-post"),
 *                         @OA\Property(property="guard_name", type="string", example="web"),
 *                         @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                         @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *                     )
 *                 ),
 *                 @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2024-11-27T10:00:00Z")
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="The specified role does not exist.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Internal server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function getRoleWithPermissions(Request $request, $id)
{
    try {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            // Log activity for not found
            activity('Roles')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to retrieve role with ID: $id",
                ])
                ->log('Role Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => ['The specified role does not exist.'],
            ], 404);
        }

        // Log activity for success
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'role' => $role,
                'description' => "Retrieved role with ID: $id",
            ])
            ->log('Role Retrieved with Permissions');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Role retrieved successfully.',
            ],
            'result' => $role,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Get Role with Permissions Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Internal server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Put(
 *     path="/v1/roles/{id}",
 *     tags={"Role & Permission"},
 *     summary="Update a specific role",
 *     description="Updates the details of a specific role by role ID. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the role to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Editor"),
 *             @OA\Property(property="guard_name", type="string", example="web")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Role updated successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Editor"),
 *                 @OA\Property(property="guard_name", type="string", example="web"),
 *                 @OA\Property(property="created_at", type="string", example="2024-11-27T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2024-11-27T12:00:00Z")
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="The specified role does not exist.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="The name field is required.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Internal server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function updateRole(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'success' => false,
                    'message' => 'Validation error.',
                ],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::find($id);

        if (!$role) {
            // Log activity for not found
            activity('Roles')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to find role with ID: $id",
                ])
                ->log('Role Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => ['The specified role does not exist.'],
            ], 404);
        }

        $role->update($request->only(['name', 'guard_name']));

        // Log activity for success
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Updated role details',
                'role_details' => $role->toArray(),
            ])
            ->log('Role Updated');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Role updated successfully.',
            ],
            'result' => $role,
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Update Role Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Internal server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


/**
 * @OA\Put(
 *     path="/v1/roles/{id}/permissions",
 *     tags={"Role & Permission"},
 *     summary="Update role details and manage permissions",
 *     description="Updates a role's details and assigns or detaches permissions. At least one permission is required. JWT Bearer token is required.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the role to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "permissions"},
 *             @OA\Property(property="name", type="string", example="Editor"),
 *             @OA\Property(property="guard_name", type="string", example="web"),
 *             @OA\Property(property="permissions", type="array",
 *                 description="Array of permission IDs to assign to the role",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Role updated and permissions updated successfully.")
 *             ),
 *             @OA\Property(property="result", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Editor"),
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="string", example="edit articles")
 *                 ),
 *                 @OA\Property(property="updated_at", type="string", example="2024-11-27T12:00:00Z")
 *             ),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="An empty array."))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Role not found.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="The specified role does not exist.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=422),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="permissions", type="array",
 *                     @OA\Items(type="string", example="At least one permission is required.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Internal server error.")
 *             ),
 *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="errors", type="array",
 *                 @OA\Items(type="string", example="An unexpected error occurred.")
 *             )
 *         )
 *     )
 * )
 */
public function updateRoleWithPermissions(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'success' => false,
                    'message' => 'Validation error.',
                ],
                'result' => [],
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::find($id);

        if (!$role) {
            // Log activity for not found
            activity('Roles')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'description' => "Failed to find role with ID: $id",
                ])
                ->log('Role Not Found');

            return response()->json([
                'meta' => [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Role not found.',
                ],
                'result' => [],
                'errors' => ['The specified role does not exist.'],
            ], 404);
        }

        // Update the role name and guard
        $role->update($request->only(['name', 'guard_name']));

        // Sync permissions
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        // Log activity
        activity('Roles')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'description' => 'Updated role and permissions',
                'role_details' => $role->toArray(),
                'permissions' => $permissions->pluck('name')->toArray(),
            ])
            ->log('Role Updated with Permissions');

        return response()->json([
            'meta' => [
                'code' => 200,
                'success' => true,
                'message' => 'Role updated and permissions updated successfully.',
            ],
            'result' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $permissions->pluck('name'),
                'updated_at' => $role->updated_at,
            ],
            'errors' => [],
        ], 200);
    } catch (\Exception $e) {
        Log::error('Update Role with Permissions Error: ' . $e->getMessage());

        return response()->json([
            'meta' => [
                'code' => 500,
                'success' => false,
                'message' => 'Internal server error.',
            ],
            'result' => [],
            'errors' => [$e->getMessage()],
        ], 500);
    }
}


}
