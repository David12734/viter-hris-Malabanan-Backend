import { devNavUrl, urlDeveloper } from "../functions/functions-general";
import Dashboard from "../pages/developer/dashboard/Dashboard";
import Employees from "../pages/developer/employees/Employees";
import Memo from "../pages/developer/memo/Memo";
import Department from "../pages/developer/settings/department/Department";
import DirectReport from "../pages/developer/settings/direct-report/DirectReport";
import Roles from "../pages/developer/settings/roles/Roles";
import Users from "../pages/developer/settings/users/Users";

export const routesDeveloper = [
  {
    path: `${devNavUrl}/${urlDeveloper}`,
    element: (
      <>
        <Employees />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/employees`,
    element: (
      <>
        <Employees />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/dashboard`,
    element: (
      <>
        <Dashboard />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/memo`,
    element: (
      <>
        <Memo />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/settings/department`,
    element: (
      <>
        <Department />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/settings/direct-report`,
    element: (
      <>
        <DirectReport />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/settings/roles`,
    element: (
      <>
        <Roles />
      </>
    ),
  },
  {
    path: `${devNavUrl}/${urlDeveloper}/settings/users`,
    element: (
      <>
        <Users />
      </>
    ),
  },
];
