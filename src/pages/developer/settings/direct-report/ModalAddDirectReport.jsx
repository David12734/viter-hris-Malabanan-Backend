import React from "react";
import { StoreContext } from "../../../../store/StoreContext";
import * as Yup from "yup";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { queryData } from "../../../../functions/custom-hooks/queryData";
import { apiVersion } from "../../../../functions/functions-general";
import {
  setError,
  setIsAdd,
  setMessage,
  setSuccess,
} from "../../../../store/StoreAction";
import ModalWrapperSide from "../../../../partials/modals/ModalWrapperSide";
import { FaTimes } from "react-icons/fa";
import { Formik, Form } from "formik";
import { InputSelect } from "../../../../components/form-inputs/FormInputs";
import ButtonSpinner from "../../../../partials/spinners/ButtonSpinner";
import MessageError from "../../../../partials/MessageError";

const ModalAddDirectReport = ({ itemEdit, activeEmployees }) => {
  const { store, dispatch } = React.useContext(StoreContext);
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: (values) =>
      queryData(
        itemEdit
          ? `${apiVersion}/controllers/developers/settings/direct-report/direct-report.php?id=${itemEdit.direct_report_aid}`
          : `${apiVersion}/controllers/developers/settings/direct-report/direct-report.php`,
        itemEdit ? "put" : "post",
        values,
      ),
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ["direct-report"] });
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      queryClient.invalidateQueries({ queryKey: ["employees-all"] });

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
    direct_report_subordinate_id: itemEdit
      ? itemEdit.direct_report_subordinate_id
      : "",
    direct_report_supervisor_id: itemEdit
      ? itemEdit.direct_report_supervisor_id
      : "",
  };

  const yupSchema = Yup.object({
    direct_report_subordinate_id: Yup.string().trim().required("Required"),
    direct_report_supervisor_id: Yup.string()
      .trim()
      .required("Required")
      .notOneOf(
        [Yup.ref("direct_report_subordinate_id")],
        "The same employee cannot be both supervisor and subordinate.",
      ),
  });

  const handleClose = () => dispatch(setIsAdd(false));

  React.useEffect(() => {
    dispatch(setError(false));
  }, [dispatch]);

  const getEmployeeName = (item) =>
    `${item.employee_first_name} ${item.employee_middle_name ? `${item.employee_middle_name} ` : ""}${item.employee_last_name}`.trim();

  return (
    <ModalWrapperSide handleClose={handleClose}>
      <div className="modal-header relative mb-4">
        <h3 className="text-dark text-sm">
          {itemEdit ? "Update" : "Add"} Direct Report
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
                    <InputSelect
                      label="Subordinate"
                      name="direct_report_subordinate_id"
                      disabled={mutation.isPending}
                    >
                      <optgroup label="Select a subordinate">
                        <option value="" hidden>
                          --
                        </option>
                        {(activeEmployees || []).map((item) => (
                          <option key={item.employee_aid} value={item.employee_aid}>
                            {getEmployeeName(item)}
                          </option>
                        ))}
                      </optgroup>
                    </InputSelect>
                  </div>
                  <div className="relative mb-6">
                    <InputSelect
                      label="Supervisor"
                      name="direct_report_supervisor_id"
                      disabled={mutation.isPending}
                    >
                      <optgroup label="Select a supervisor">
                        <option value="" hidden>
                          --
                        </option>
                        {(activeEmployees || []).map((item) => (
                          <option key={item.employee_aid} value={item.employee_aid}>
                            {getEmployeeName(item)}
                          </option>
                        ))}
                      </optgroup>
                    </InputSelect>
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

export default ModalAddDirectReport;
