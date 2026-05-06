import React from "react";
import Layout from "../Layout";
import { FaPlus } from "react-icons/fa";
import { StoreContext } from "../../../store/StoreContext";
import { setIsAdd } from "../../../store/StoreAction";
import EmployeesList from "./EmployeesList";
import ModalAddEmployee from "./ModalAddEmployee";
import useQueryData from "../../../functions/custom-hooks/useQueryData";
import { apiVersion } from "../../../functions/functions-general";
import ButtonSpinner from "../../../partials/spinners/ButtonSpinner";

const Employees = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);

  // fetch active departments for the dropdown
  const { isLoading, data: dataDepartment } = useQueryData(
    `${apiVersion}/controllers/developers/settings/department/department.php`,
    "get",
    "department",
  );

  const activeDepartments = dataDepartment?.data?.filter(
    (item) => item.department_is_active == 1,
  );

  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };

  return (
    <>
      <Layout menu="employees">
        {/* page header */}
        <div className="flex items-center w-full justify-between">
          <h1>Employees</h1>
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
        {/* page content */}
        <div>
          <EmployeesList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && (
        <ModalAddEmployee
          itemEdit={itemEdit}
          filterArrayActiveDepartments={activeDepartments || []}
        />
      )}
    </>
  );
};

export default Employees;