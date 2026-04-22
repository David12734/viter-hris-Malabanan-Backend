import React from "react";
import Layout from "../../Layout";
import UsersList from "./UsersList";
import { setIsAdd } from "../../../../store/StoreAction";
import { StoreContext } from "../../../../store/StoreContext";
import { FaPlus } from "react-icons/fa";
import ModalAddUsers from "./ModalAddUsers";
import useQueryData from "../../../../functions/custom-hooks/useQueryData";
import { apiVersion } from "../../../../functions/functions-general";
import ButtonSpinner from "../../../../partials/spinners/ButtonSpinner";

const Users = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);

  const {
    isLoading,
    isFetching,
    data: dataRoles,
  } = useQueryData(
    `${apiVersion}/controllers/developers/settings/roles/roles.php`, //api path file
    "get", //method reques(get,post,put,delete)
    "roles", //query key
  );

  const filterArrayActiveRoles = dataRoles?.data.filter(
    (item) => item.role_is_active == 1,
  );

  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };
  return (
    <>
      <Layout menu="settings" submenu="users">
        {/* page header */}
        <div className="flex items-center w-full justify-between">
          <h1>Users</h1>
          <div>
            {isLoading ? (
              <ButtonSpinner />
            ) : (
              <button
                type="button"
                className="flex items=center gap-1 hover:underline"
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
          <UsersList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && (
        <ModalAddUsers
          itemEdit={itemEdit}
          filterArrayActiveRoles={filterArrayActiveRoles}
        />
      )}
    </>
  );
};

export default Users;
