import React from "react";
import Layout from "../Layout";
import { FaPlus } from "react-icons/fa";
import { StoreContext } from "../../../store/StoreContext";
import { setIsAdd } from "../../../store/StoreAction";
import MemoList from "./MemoList";
import ModalAddMemo from "./ModalAddMemo";

const Memo = () => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [itemEdit, setItemEdit] = React.useState(null);

  const handleAdd = () => {
    dispatch(setIsAdd(true));
    setItemEdit(null);
  };

  return (
    <>
      <Layout menu="memo">
        {/* page header */}
        <div className="flex items-center w-full justify-between">
          <h1>Memo</h1>
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
          <MemoList itemEdit={itemEdit} setItemEdit={setItemEdit} />
        </div>
      </Layout>
      {store.isAdd && <ModalAddMemo itemEdit={itemEdit} />}
    </>
  );
};

export default Memo;