import React from "react";
import Layout from "../../Layout";
import { FaPlus } from "react-icons/fa";
import { StoreContext } from "../../../../store/StoreContext";
import { setIsAdd } from "../../../../store/StoreAction";
import DepartmentList from "./DepartmentList";
import ModalAddDepartment from "./ModalAddDepartment";

const Department = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);

  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };

  return (
    <>
      <Layout menu="settings" submenu="department">
        {/* page header */}
        <div className="flex items-center w-full justify-between">
          <h1>Department</h1>
          <div>
            <button
              type="button"
              className="flex items-center gap-1 hover:underline"
              onClick={handleAdd}
            >
              <FaPlus className="text-primary" />
              Add
            </button>
          </div>
        </div>
        {/* page content */}
        <div>
          <DepartmentList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && <ModalAddDepartment itemEdit={itemEdit} />}
    </>
  );
};

export default Department;