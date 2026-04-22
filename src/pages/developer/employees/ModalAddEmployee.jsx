import React from "react";
import { StoreContext } from "../../../store/StoreContext";
import * as Yup from "yup";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { queryData } from "../../../functions/custom-hooks/queryData";
import { apiVersion } from "../../../functions/functions-general";
import {
  setError,
  setIsAdd,
  setMessage,
  setSuccess,
} from "../../../store/StoreAction";
import ModalWrapperSide from "../../../partials/modals/ModalWrapperSide";
import { FaTimes } from "react-icons/fa";
import { Formik, Form } from "formik";
import { InputText } from "../../../components/form-inputs/FormInputs";
import ButtonSpinner from "../../../partials/spinners/ButtonSpinner";
import MessageError from "../../../partials/MessageError";

const ModalAddEmployee = ({ itemEdit }) => {
  const { store, dispatch } = React.useContext(StoreContext);
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: (values) =>
      queryData(
        itemEdit
          ? `${apiVersion}/controllers/developers/employees/employees.php?id=${itemEdit.employee_aid}`
          : `${apiVersion}/controllers/developers/employees/employees.php`,
        itemEdit ? "put" : "post",
        values,
      ),
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });

      if (data.success) {
        dispatch(setSuccess(true));
        dispatch(setMessage(`Successfully ${itemEdit ? "updated" : "added"}`));
        dispatch(setIsAdd(false));
      }

      if (data.success === false) {
        dispatch(setError(true));
        dispatch(setMessage(data.error));
      }
    },
  });

  const initVal = {
    ...itemEdit,
    employee_first_name: itemEdit ? itemEdit.employee_first_name : "",
    employee_middle_name: itemEdit ? itemEdit.employee_middle_name : "",
    employee_last_name: itemEdit ? itemEdit.employee_last_name : "",
    employee_email: itemEdit ? itemEdit.employee_email : "",
    employee_first_name_old: itemEdit ? itemEdit.employee_first_name : "",
    employee_email_old: itemEdit ? itemEdit.employee_email : "",
  };

  const yupSchema = Yup.object({
    employee_first_name: Yup.string().trim().required("required"),
    employee_middle_name: Yup.string().trim(),
    employee_last_name: Yup.string().trim().required("required"),
    employee_email: Yup.string().trim().email("Invalid email").required("required"),
  });

  const handleClose = () => {
    dispatch(setIsAdd(false));
  };

  React.useEffect(() => {
    dispatch(setError(false));
  }, [dispatch]);

  return (
    <ModalWrapperSide
      handleClose={handleClose}
      className="transition-all ease-in-out transform duration-200"
    >
      <div className="moda-header relative mb-4">
        <h3 className="text-dark text-sm">
          {itemEdit ? "Update" : "Add"} Employee
        </h3>
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
          {(props) => (
            <Form className="h-full">
              <div className="modal-form-container">
                <div className="modal-container">
                  <div className="relative mb-6">
                    <InputText
                      label="First Name"
                      name="employee_first_name"
                      type="text"
                      disabled={mutation.isPending}
                    />
                  </div>
                  <div className="relative mb-6">
                    <InputText
                      label="Middle Name"
                      name="employee_middle_name"
                      type="text"
                      required={false}
                      disabled={mutation.isPending}
                    />
                  </div>
                  <div className="relative mb-6">
                    <InputText
                      label="Last Name"
                      name="employee_last_name"
                      type="text"
                      disabled={mutation.isPending}
                    />
                  </div>
                  <div className="relative mb-6">
                    <InputText
                      label="Email"
                      name="employee_email"
                      type="email"
                      disabled={mutation.isPending}
                    />
                  </div>
                  {store.error && <MessageError />}
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
          )}
        </Formik>
      </div>
    </ModalWrapperSide>
  );
};

export default ModalAddEmployee;
