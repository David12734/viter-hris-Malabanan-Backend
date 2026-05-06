import React from "react";
import Layout from "../Layout";
import useQueryData from "../../../functions/custom-hooks/useQueryData";
import { apiVersion } from "../../../functions/functions-general";
import { FaBirthdayCake, FaBullhorn, FaUserFriends, FaUserTie } from "react-icons/fa";
import { MdPersonOff } from "react-icons/md";

// ── Avatar: shows initials in a colored circle ──
const Avatar = ({ firstName = "", lastName = "", size = "md" }) => {
  const initials = `${firstName?.[0] ?? ""}${lastName?.[0] ?? ""}`.toUpperCase();
  const sizeClass = size === "sm" ? "w-8 h-8 text-xs" : size === "lg" ? "w-12 h-12 text-base" : "w-10 h-10 text-sm";
  return (
    <div className={`${sizeClass} rounded-full bg-primary text-white flex items-center justify-center font-bold flex-shrink-0`}>
      {initials}
    </div>
  );
};

// ── Format birthday display ──
const formatBirthday = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("en-US", { month: "long", day: "numeric" });
};

// ── Compute years of service ──
const yearsOfService = (dateStr) => {
  if (!dateStr) return 0;
  const start = new Date(dateStr);
  const now = new Date();
  return now.getFullYear() - start.getFullYear();
};

// ── Section card wrapper ──
const Card = ({ icon, title, color = "text-primary", children }) => (
  <div className="bg-white rounded-lg border border-gray-100 shadow-sm">
    <div className="flex items-center gap-2 px-4 py-3 border-b border-gray-100">
      <span className={color}>{icon}</span>
      <h2 className="font-bold text-sm">{title}</h2>
    </div>
    <div className="p-4">{children}</div>
  </div>
);

const Dashboard = () => {
  const { isLoading, data } = useQueryData(
    `${apiVersion}/controllers/developers/dashboard/dashboard.php`,
    "get",
    "dashboard",
    {},
    null,
    true,
  );

  const dashboardData = data?.data ?? data ?? {};
  const memos         = dashboardData?.memos         ?? [];
  const employees     = dashboardData?.employees     ?? [];
  const birthdays     = dashboardData?.birthdays     ?? [];
  const anniversaries = dashboardData?.anniversaries ?? [];
  const newEmployees  = dashboardData?.new_employees ?? [];

  // Group employees by department
  const grouped = employees.reduce((acc, emp) => {
    const dept = emp.department_name ?? "No Department";
    if (!acc[dept]) acc[dept] = [];
    acc[dept].push(emp);
    return acc;
  }, {});

  // Today's birthday check
  const today = new Date();
  const todayMonth = today.getMonth() + 1;
  const todayDay   = today.getDate();
  const isTodayBirthday = (dateStr) => {
    if (!dateStr) return false;
    const d = new Date(dateStr);
    return d.getMonth() + 1 === todayMonth && d.getDate() === todayDay;
  };

  const firstName = "John";
  const lastName  = "Doe";

  if (isLoading) {
    return (
      <Layout menu="dashboard">
        <div className="flex items-center justify-center h-64 text-gray-400 text-sm">Loading dashboard...</div>
      </Layout>
    );
  }

  return (
    <Layout menu="dashboard">
      {/* PAGE HEADER */}
      <div className="flex items-center gap-3 mb-6">
        <Avatar firstName={firstName} lastName={lastName} size="lg" />
        <div>
          <h1 className="text-xl font-bold">Welcome {lastName}, {firstName}!</h1>
          <p className="text-xs text-gray-400">{new Date().toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric", year: "numeric" })}</p>
        </div>
      </div>

      {/* GRID LAYOUT */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {/* ── WHO'S OUT (static for now) ── */}
        <Card icon={<MdPersonOff />} title="Who's Out" color="text-primary">
          <p className="text-xs font-semibold text-gray-500 uppercase mb-2">TODAY</p>
          <div className="text-sm text-gray-400 italic">No one is out today.</div>
          <p className="text-xs font-semibold text-gray-500 uppercase mt-4 mb-2">TOMORROW</p>
          <div className="text-sm text-gray-400 italic">No one is out tomorrow.</div>

          {/* ── CELEBRATIONS ── */}
          <div className="mt-6">
            <div className="flex items-center gap-2 mb-3">
              <FaBirthdayCake className="text-primary" />
              <span className="font-bold text-sm">Celebrations</span>
            </div>

            {birthdays.length === 0 && anniversaries.length === 0 ? (
              <div className="text-sm text-gray-400 text-center py-4">
                🎉 No celebrations this month. We would like to express our sincere appreciation and gratitude for all the hard work of our employees.
              </div>
            ) : (
              <>
                {birthdays.length > 0 && (
                  <div className="mb-3">
                    <p className="text-xs text-gray-500 font-semibold mb-2">🎂 Birthdays this month</p>
                    <div className="space-y-2">
                      {birthdays.map((emp, key) => (
                        <div key={key} className="flex items-center gap-3">
                          <Avatar firstName={emp.employee_first_name} lastName={emp.employee_last_name} size="sm" />
                          <div>
                            <div className="text-sm font-medium">
                              {emp.employee_last_name}, {emp.employee_first_name}
                              {isTodayBirthday(emp.employee_birthday) && (
                                <span className="ml-2 text-xs bg-primary text-white rounded-full px-2 py-0.5">Today! 🎉</span>
                              )}
                            </div>
                            <div className="text-xs text-gray-400">{formatBirthday(emp.employee_birthday)}</div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
                {anniversaries.length > 0 && (
                  <div>
                    <p className="text-xs text-gray-500 font-semibold mb-2">🏆 Work Anniversaries this month</p>
                    <div className="space-y-2">
                      {anniversaries.map((emp, key) => (
                        <div key={key} className="flex items-center gap-3">
                          <Avatar firstName={emp.employee_first_name} lastName={emp.employee_last_name} size="sm" />
                          <div>
                            <div className="text-sm font-medium">{emp.employee_last_name}, {emp.employee_first_name}</div>
                            <div className="text-xs text-gray-400">{yearsOfService(emp.employee_start_work_date)} year(s) — {formatBirthday(emp.employee_start_work_date)}</div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </>
            )}
          </div>
        </Card>

        {/* ── ANNOUNCEMENTS / MEMOS ── */}
        <Card icon={<FaBullhorn />} title="Announcement" color="text-primary">
          {memos.length === 0 ? (
            <div className="text-sm text-gray-400 italic">No announcements yet.</div>
          ) : (
            <div className="space-y-5 max-h-[600px] overflow-y-auto pr-1">
              {memos.map((memo, key) => (
                <div key={key} className="border-b border-gray-100 pb-4 last:border-0">
                  <div className="flex items-start gap-2 mb-1">
                    <FaBullhorn className="text-primary mt-0.5 flex-shrink-0" />
                    <div>
                      <p className="font-bold text-sm">{memo.memo_category}</p>
                      <p className="text-xs text-gray-400">Date: {memo.memo_date}</p>
                    </div>
                  </div>
                  <p className="text-sm text-gray-600 whitespace-pre-wrap mt-2 leading-relaxed">
                    {memo.memo_text}
                  </p>
                  <p className="text-xs text-gray-400 mt-1">— {memo.memo_from}</p>
                </div>
              ))}
            </div>
          )}
        </Card>

        {/* ── NEW EMPLOYEES ── */}
        <Card icon={<FaUserTie />} title="Welcome to Frontline Business Solutions Inc." color="text-primary">
          {newEmployees.length === 0 ? (
            <div className="text-sm text-gray-400 italic text-center py-4">No new employee yet.</div>
          ) : (
            <div className="space-y-3">
              {newEmployees.map((emp, key) => (
                <div key={key} className="flex items-center gap-3">
                  <Avatar firstName={emp.employee_first_name} lastName={emp.employee_last_name} />
                  <div>
                    <div className="text-sm font-medium">{emp.employee_last_name}, {emp.employee_first_name}</div>
                    <div className="text-xs text-gray-400">{emp.department_name ?? "--"}</div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>

        {/* ── MY TEAM (grouped by department) ── */}
        <Card icon={<FaUserFriends />} title="My Team" color="text-primary">
          {employees.length === 0 ? (
            <div className="text-sm text-gray-400 italic text-center py-4">No employees found.</div>
          ) : (
            <div className="space-y-4 max-h-[400px] overflow-y-auto pr-1">
              {Object.entries(grouped).map(([dept, emps], key) => (
                <div key={key}>
                  <p className="text-xs font-bold text-gray-500 uppercase mb-2">{dept}</p>
                  <div className="grid grid-cols-3 gap-3">
                    {emps.map((emp, k) => (
                      <div key={k} className="flex flex-col items-center text-center gap-1">
                        <Avatar firstName={emp.employee_first_name} lastName={emp.employee_last_name} />
                        <div className="text-xs font-medium leading-tight">{emp.employee_last_name}, {emp.employee_first_name}</div>
                        <div className="text-xs text-gray-400">{dept}</div>
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>

      </div>
    </Layout>
  );
};

export default Dashboard;
