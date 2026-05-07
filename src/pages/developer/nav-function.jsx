import { FaCogs, FaFileAlt, FaUsers } from "react-icons/fa";
import { MdDashboard } from "react-icons/md";
import { devNavUrl, urlDeveloper } from "../../functions/functions-general";

export const navList = [
  {
    label: "Dashboard",
    icon: <MdDashboard />,
    menu: "dashboard",
    path: `${devNavUrl}/${urlDeveloper}/dashboard`,
    submenu: "",
  },
  {
    label: "Employees",
    icon: <FaUsers />,
    menu: "employees",
    path: `${devNavUrl}/${urlDeveloper}/employees`,
    submenu: "",
  },
  {
    label: "Memo",
    icon: <FaFileAlt />,
    menu: "memo",
    path: `${devNavUrl}/${urlDeveloper}/memo`,
    submenu: "",
  },
  {
    label: "Settings",
    icon: <FaCogs />,
    menu: "settings",
    submenu: "",
    subNavList: [
      {
        label: "Department",
        path: `${devNavUrl}/${urlDeveloper}/settings/department`,
      },
      {
        label: "Direct Report",
        path: `${devNavUrl}/${urlDeveloper}/settings/direct-report`,
      },
      {
        label: "Roles",
        path: `${devNavUrl}/${urlDeveloper}/settings/roles`,
      },
      {
        label: "Users",
        path: `${devNavUrl}/${urlDeveloper}/settings/users`,
      },
    ],
  },
];
