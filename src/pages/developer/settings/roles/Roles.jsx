import React from "react";
import Layout from "../../Layout";
import RolesList from "./RolesList";
import { setIsAdd } from "../../../../store/StoreAction";
import { StoreContext } from "../../../../store/StoreContext";
import { FaPlus } from "react-icons/fa";
import ModalAddRoles from "./ModalAddRoles";

const Roles = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);
  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };
  return (
    <>
      <Layout menu="settings" submenu="roles">
        {/* PAGE HEADER */}
        <div className="flex items-center justify-between w-full">
          <h1>Roles</h1>
          <div>
            <button
              type="button"
              className="flex items-center gap-1 hover:underline"
              onClick={handleAdd}
            >
              <FaPlus className="text-primary" />
              Add Role
            </button>
          </div>
        </div>
        {/* PAGE CONTENT */}
        <div>
          <RolesList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && <ModalAddRoles itemEdit={itemEdit} />}
    </>
  );
};

export default Roles;
