import React from "react";
import * as Yup from "yup";
import { Form, Formik } from "formik";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { FaTimes } from "react-icons/fa";
import {
  InputText,
  InputTextArea,
} from "../../../components/form-inputs/FormInputs";
import { queryData } from "../../../functions/custom-hooks/queryData";
import { apiVersion } from "../../../functions/functions-general";
import MessageError from "../../../partials/MessageError";
import ModalWrapperSide from "../../../partials/modals/ModalWrapperSide";
import ButtonSpinner from "../../../partials/spinners/ButtonSpinner";
import {
  setError,
  setIsAdd,
  setMessage,
  setSuccess,
} from "../../../store/StoreAction";
import { StoreContext } from "../../../store/StoreContext";

const ModalAddMemo = ({ itemEdit }) => {
  const { store, dispatch } = React.useContext(StoreContext);
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: (values) =>
      queryData(
        itemEdit
          ? `${apiVersion}/controllers/developers/memo/memo.php?id=${itemEdit.memo_aid}`
          : `${apiVersion}/controllers/developers/memo/memo.php`,
        itemEdit ? "put" : "post",
        values,
      ),
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ["memo"] });
      queryClient.invalidateQueries({ queryKey: ["dashboard"], exact: false });

      if (data.success) {
        dispatch(setSuccess(true));
        dispatch(setMessage(`Successfully ${itemEdit ? "updated" : "added"}`));
        dispatch(setIsAdd(false));
      }

      if (data.success == false) {
        dispatch(setError(true));
        dispatch(setMessage(data.error));
      }
    },
  });

  const initVal = {
    ...itemEdit,
    memo_from: itemEdit ? itemEdit.memo_from : "",
    memo_to: itemEdit ? itemEdit.memo_to : "",
    memo_date: itemEdit ? itemEdit.memo_date : "",
    memo_category: itemEdit ? itemEdit.memo_category : "",
    memo_text: itemEdit ? itemEdit.memo_text : "",
  };

  const yupSchema = Yup.object({
    memo_from: Yup.string().trim().required("required"),
    memo_to: Yup.string().trim().required("required"),
    memo_date: Yup.string().trim().required("required"),
    memo_category: Yup.string().trim().required("required"),
    memo_text: Yup.string().trim().required("required"),
  });

  const handleClose = () => {
    dispatch(setIsAdd(false));
  };

  React.useEffect(() => {
    dispatch(setError(false));
  }, []);

  return (
    <ModalWrapperSide
      handleClose={handleClose}
      className="transition-all ease-in-out transform duration-200"
    >
      <div className="modal-header relative mb-4">
        <h3 className="text-dark text-sm">{itemEdit ? "Update" : "Add"} Memo</h3>
        <button
          type="button"
          className="absolute top-0 right-4"
          onClick={handleClose}
        >
          <FaTimes />
        </button>
      </div>

      <div className="modal-body">
        <Formik
          initialValues={initVal}
          validationSchema={yupSchema}
          onSubmit={async (values) => {
            dispatch(setError(false));
            mutation.mutate(values);
          }}
        >
          {(props) => {
            return (
              <Form className="h-full">
                <div className="modal-form-container">
                  <div className="modal-container">
                    <div className="relative mb-6">
                      <InputText
                        label="From"
                        name="memo_from"
                        type="text"
                        disabled={mutation.isPending}
                      />
                    </div>

                    <div className="relative mb-6">
                      <InputText
                        label="To"
                        name="memo_to"
                        type="text"
                        disabled={mutation.isPending}
                      />
                    </div>

                    <div className="relative mb-6">
                      <InputText
                        label="Date"
                        name="memo_date"
                        type="date"
                        disabled={mutation.isPending}
                      />
                    </div>

                    <div className="relative mb-6">
                      <InputText
                        label="Category"
                        name="memo_category"
                        type="text"
                        disabled={mutation.isPending}
                      />
                    </div>

                    <div className="relative mb-6">
                      <InputTextArea
                        label="Memo Text"
                        name="memo_text"
                        rows="10"
                        disabled={mutation.isPending}
                      />

                      {store.error && <MessageError />}
                    </div>
                  </div>
                  <div className="modal-action">
                    <button
                      type="submit"
                      disabled={mutation.isPending || !props.dirty}
                      className="btn-modal-submit"
                    >
                      {mutation.isPending ? (
                        <ButtonSpinner />
                      ) : itemEdit ? (
                        "Save"
                      ) : (
                        "Add"
                      )}
                    </button>
                    <button
                      type="reset"
                      className="btn-modal-cancel"
                      onClick={handleClose}
                      disabled={mutation.isPending}
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              </Form>
            );
          }}
        </Formik>
      </div>
    </ModalWrapperSide>
  );
};

export default ModalAddMemo;