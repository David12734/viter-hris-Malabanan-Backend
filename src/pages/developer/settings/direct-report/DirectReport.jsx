import React from "react";
import Layout from "../../Layout";
import { FaPlus } from "react-icons/fa";
import { StoreContext } from "../../../../store/StoreContext";
import { setIsAdd } from "../../../../store/StoreAction";
import { apiVersion } from "../../../../functions/functions-general";
import useQueryData from "../../../../functions/custom-hooks/useQueryData";
import ButtonSpinner from "../../../../partials/spinners/ButtonSpinner";
import DirectReportList from "./DirectReportList";
import ModalAddDirectReport from "./ModalAddDirectReport";

const DirectReport = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);

  const { isLoading, data: dataEmployees } = useQueryData(
    `${apiVersion}/controllers/developers/employees/employees.php`,
    "get",
    "employees-all",
  );

  const activeEmployees = dataEmployees?.data?.filter(
    (item) => item.employee_is_active == 1,
  );

  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };

  return (
    <>
      <Layout menu="settings" submenu="direct-report">
        <div className="flex items-center w-full justify-between">
          <h1>Direct Report</h1>
          <div>
            {isLoading ? (
              <ButtonSpinner />
            ) : (
              <button
                type="button"
                className="flex items-center gap-1 hover:underline"
                onClick={handleAdd}
              >
                <FaPlus className="text-primary" />
                Add
              </button>
            )}
          </div>
        </div>

        <div>
          <DirectReportList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && (
        <ModalAddDirectReport
          itemEdit={itemEdit}
          activeEmployees={activeEmployees || []}
        />
      )}
    </>
  );
};

export default DirectReport;
