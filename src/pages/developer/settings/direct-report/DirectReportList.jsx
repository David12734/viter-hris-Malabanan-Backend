import React from "react";
import { StoreContext } from "../../../../store/StoreContext";
import { useInfiniteQuery } from "@tanstack/react-query";
import { queryDataInfinite } from "../../../../functions/custom-hooks/queryDataInfinite";
import { apiVersion } from "../../../../functions/functions-general";
import { useInView } from "react-intersection-observer";
import NoData from "../../../../partials/NoData";
import ServerError from "../../../../partials/ServerError";
import TableLoading from "../../../../partials/TableLoading";
import FetchingSpinner from "../../../../partials/spinners/FetchingSpinner";
import Loadmore from "../../../../partials/Loadmore";
import Status from "../../../../partials/Status";
import SearchBar from "../../../../partials/SearchBar";
import { FaArchive, FaEdit, FaTrash, FaTrashRestore } from "react-icons/fa";
import {
  setIsAdd,
  setIsArchive,
  setIsDelete,
  setIsRestore,
} from "../../../../store/StoreAction";
import ModalArchive from "../../../../partials/modals/ModalArchive";
import ModalRestore from "../../../../partials/modals/ModalRestore";
import ModalDelete from "../../../../partials/modals/ModalDelete";

const DirectReportList = ({ itemEdit, setItemEdit }) => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [page, setPage] = React.useState(1);
  const [filterData, setFilterData] = React.useState("");
  const [onSearch, setOnSearch] = React.useState(false);
  const search = React.useRef({ value: "" });
  const { ref, inView } = useInView();
  let counter = 1;

  const {
    data: result,
    error,
    fetchNextPage,
    hasNextPage,
    isFetching,
    isFetchingNextPage,
    status,
  } = useInfiniteQuery({
    queryKey: ["direct-report", search.current.value, store.isSearch, filterData],
    queryFn: async ({ pageParam = 1 }) =>
      await queryDataInfinite(
        ``,
        `${apiVersion}/controllers/developers/settings/direct-report/page.php?start=${pageParam}`,
        false,
        {
          filterData,
          searchValue: search?.current?.value,
        },
        `post`,
      ),
    getNextPageParam: (lastPage) => {
      if (lastPage.page < lastPage.total) {
        return lastPage.page + lastPage.count;
      }
      return;
    },
    refetchOnWindowFocus: false,
  });

  React.useEffect(() => {
    if (inView) {
      setPage((prev) => prev + 1);
      fetchNextPage();
    }
  }, [inView, fetchNextPage]);

  const getEmployeeName = (firstName, lastName) =>
    `${firstName || ""} ${lastName || ""}`.trim() || "--";

  const handleEdit = (item) => {
    dispatch(setIsAdd(true));
    setItemEdit(item);
  };

  const handleArchive = (item) => {
    dispatch(setIsArchive(true));
    setItemEdit(item);
  };

  const handleRestore = (item) => {
    dispatch(setIsRestore(true));
    setItemEdit(item);
  };

  const handleDelete = (item) => {
    dispatch(setIsDelete(true));
    setItemEdit(item);
  };

  return (
    <>
      <div className="flex items-center justify-between">
        <div className="relative">
          <label htmlFor="">Status</label>
          <select
            onChange={(e) => setFilterData(e.target.value)}
            value={filterData}
          >
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <SearchBar
          search={search}
          dispatch={dispatch}
          store={store}
          result={result?.pages}
          isFetching={isFetching}
          setOnSearch={setOnSearch}
          onSearch={onSearch}
        />
      </div>

      <div className="relative pt-4 rounded-md">
        {status !== "pending" && isFetching && <FetchingSpinner />}
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Status</th>
              <th>Name</th>
              <th>Supervisor</th>
              <th>Supervisor Email</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {!error &&
              (status === "pending" || result?.pages[0]?.count === 0) && (
                <tr>
                  <td colSpan="100%" className="p-10">
                    {status === "pending" ? (
                      <TableLoading cols={2} count={20} />
                    ) : (
                      <NoData />
                    )}
                  </td>
                </tr>
              )}
            {error && (
              <tr>
                <td colSpan="100%" className="p-10">
                  <ServerError />
                </td>
              </tr>
            )}
            {result?.pages?.map((page, key) => (
              <React.Fragment key={key}>
                {page?.data?.map((item, key) => (
                  <tr key={key}>
                    <td>{counter++}</td>
                    <td>
                      <Status
                        text={`${item.direct_report_is_active == 1 ? "active" : "inactive"}`}
                      />
                    </td>
                    <td>
                      {getEmployeeName(
                        item.subordinate_first_name,
                        item.subordinate_last_name,
                      )}
                    </td>
                    <td>
                      {getEmployeeName(
                        item.supervisor_first_name,
                        item.supervisor_last_name,
                      )}
                    </td>
                    <td>{item.supervisor_email || "--"}</td>
                    <td>
                      <div className="flex items-center gap-3">
                        {item.direct_report_is_active == 1 ? (
                          <>
                            <button
                              type="button"
                              className="tooltip-action-table"
                              data-tooltip="Edit"
                              onClick={() => handleEdit(item)}
                            >
                              <FaEdit />
                            </button>
                            <button
                              type="button"
                              className="tooltip-action-table"
                              data-tooltip="Archive"
                              onClick={() => handleArchive(item)}
                            >
                              <FaArchive />
                            </button>
                          </>
                        ) : (
                          <>
                            <button
                              type="button"
                              className="tooltip-action-table"
                              data-tooltip="Restore"
                              onClick={() => handleRestore(item)}
                            >
                              <FaTrashRestore />
                            </button>
                            <button
                              type="button"
                              className="tooltip-action-table"
                              data-tooltip="Delete"
                              onClick={() => handleDelete(item)}
                            >
                              <FaTrash />
                            </button>
                          </>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </React.Fragment>
            ))}
          </tbody>
        </table>

        <div className="loadmore flex justify-center flex-col items-center pb-10">
          <Loadmore
            fetchNextPage={fetchNextPage}
            isFetchingNextPage={isFetchingNextPage}
            hasNextPage={hasNextPage}
            result={result?.pages[0]}
            setPage={setPage}
            page={page}
            refView={ref}
            isSearchOrFilter={store.isSearch || result?.isFilter}
          />
        </div>
      </div>

      {store.isArchive && itemEdit && (
        <ModalArchive
          mysqlApiArchive={`${apiVersion}/controllers/developers/settings/direct-report/active.php?id=${itemEdit.direct_report_aid}`}
          msg="Are you sure you want to archive this record?"
          successMsg="Successfully archived."
          item={{
            name: getEmployeeName(
              itemEdit.subordinate_first_name,
              itemEdit.subordinate_last_name,
            ),
          }}
          dataItem={itemEdit}
          queryKey={["direct-report", "employees"]}
        />
      )}
      {store.isRestore && itemEdit && (
        <ModalRestore
          mysqlApiRestore={`${apiVersion}/controllers/developers/settings/direct-report/active.php?id=${itemEdit.direct_report_aid}`}
          msg="Are you sure you want to restore this record?"
          successMsg="Successfully restored."
          item={{
            name: getEmployeeName(
              itemEdit.subordinate_first_name,
              itemEdit.subordinate_last_name,
            ),
          }}
          dataItem={itemEdit}
          queryKey={["direct-report", "employees"]}
        />
      )}
      {store.isDelete && itemEdit && (
        <ModalDelete
          mysqlApiDelete={`${apiVersion}/controllers/developers/settings/direct-report/direct-report.php?id=${itemEdit.direct_report_aid}`}
          msg="Are you sure you want to delete this record?"
          successMsg="Successfully deleted."
          item={{
            name: getEmployeeName(
              itemEdit.subordinate_first_name,
              itemEdit.subordinate_last_name,
            ),
          }}
          dataItem={itemEdit}
          queryKey={["direct-report", "employees"]}
        />
      )}
    </>
  );
};

export default DirectReportList;
