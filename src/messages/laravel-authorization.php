<?php
return [
    /* middleware messages */
  "forbidden" => "You don't have an access for this resource",

    /* controller messages */
  "index" => "Roles",
  "moduledPermissions" => "Moduled Permissions",
  "moduledPermissionsNames" => "Moduled Permissions Names",
  "create" => "Role created successfully",
  "show" => "Role",
  "update" => "Role updated successfully",
  "delete" => "Role deleted successfully",

    /* updateRoleRequest */
    "immutableSuperRoleName" => "The Super Role Name is immutable you can't change it",

    /* ValidModuledPermissionsRule validation message */
  "invalidAttribute" => "The selected input :attribute has an invalid modules structure",
  "missingModules" => "The selected input :attribute has a missing module called :module",
  "extraModules" => "The selected input :attribute has an extra module called :module",
  "invalidPermissions" => "The selected input :attribute has invalid permission value :permissionsValues at :permissions at Module :module.",
  "missingPermissions" => "The selected input :attribute has a missing permissions :permissions at Module :module.",
  "extraPermissions" => "The selected input :attribute has an extra permissions :permissions at Module :module.",
];
