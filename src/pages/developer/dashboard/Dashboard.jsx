import React from "react";
import { StoreContext } from "../../../store/StoreContext";
import { setIsAdd } from "../../../store/StoreAction";
import Layout from "../Layout";
import { FaPlus } from "react-icons/fa";

const Dashboard = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);
  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };
  return (
    <>
      <Layout menu="employees">
        {/* page header */}
        <div className="flex items-center w-full justify-between">
          <h1>Dashboard</h1>
          <div>
            <button
              type="button"
              className="flex items=center gap-1 hover:underline"
              onClick={handleAdd}
            >
              <FaPlus className="text-primary" />
              Add
            </button>
          </div>
        </div>
        {/* page content */}
        <div>
          {/* <RolesList itemEdit={itemEdit} setItemEdit={setItemEdit} /> */}
        </div>
      </Layout>
      {/* {store.isAdd && <ModalAddRoles itemEdit={itemEdit} />} */}
    </>
  );
};

export default Dashboard;
