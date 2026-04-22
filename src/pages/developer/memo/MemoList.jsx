import React from "react";
import { StoreContext } from "../../../store/StoreContext";
import { useInfiniteQuery } from "@tanstack/react-query";
import { queryDataInfinite } from "../../../functions/custom-hooks/queryDataInfinite";
import { apiVersion, formatDate } from "../../../functions/functions-general";
import { useInView } from "react-intersection-observer";
import NoData from "../../../partials/NoData";
import ServerError from "../../../partials/ServerError";
import TableLoading from "../../../partials/TableLoading";
import FetchingSpinner from "../../../partials/spinners/FetchingSpinner";
import Loadmore from "../../../partials/Loadmore";
import Status from "../../../partials/Status";
import SearchBar from "../../../partials/SearchBar";
import { FaArchive, FaEdit, FaEye, FaTrash, FaTrashRestore } from "react-icons/fa";
import {
  setIsAdd,
  setIsArchive,
  setIsDelete,
  setIsRestore,
} from "../../../store/StoreAction";
import ModalArchive from "../../../partials/modals/ModalArchive";
import ModalRestore from "../../../partials/modals/ModalRestore";
import ModalDelete from "../../../partials/modals/ModalDelete";
import ModalViewMemo from "./ModalViewMemo";

const MemoList = ({ itemEdit, setItemEdit }) => {
  const { store, dispatch } = React.useContext(StoreContext);
  const [page, setPage] = React.useState(1);
  const [filterData, setFilterData] = React.useState("");
  const [onSearch, setOnSearch] = React.useState(false);
  const [isViewMemo, setIsViewMemo] = React.useState(false);
  const [viewItem, setViewItem] = React.useState(null);
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
    queryKey: ["memo", search.current.value, store.isSearch, filterData],
    queryFn: async ({ pageParam = 1 }) =>
      await queryDataInfinite(
        ``,
        `${apiVersion}/controllers/developers/memo/page.php?start=${pageParam}`,
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
  }, [inView]);

  const handleEdit = (item) => {
    dispatch(setIsAdd(true));
    setItemEdit(item);
  };

  const handleView = (item) => {
    setViewItem(item);
    setIsViewMemo(true);
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
              <th>Date</th>
              <th>From</th>
              <th>To</th>
              <th>Category</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {/* loading */}
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
            {/* error */}
            {error && (
              <tr>
                <td colSpan="100%" className="p-10">
                  <ServerError />
                </td>
              </tr>
            )}
            {/* data rows */}
            {result?.pages?.map((page, key) => (
              <React.Fragment key={key}>
                {page?.data?.map((item, key) => (
                  <tr key={key}>
                    <td>{counter++}</td>
                    <td>
                      <Status
                        text={`${item.memo_is_active == 1 ? "active" : "inactive"}`}
                      />
                    </td>
                    <td>{item.memo_date}</td>
                    <td>{item.memo_from}</td>
                    <td>{item.memo_to}</td>
                    <td>{item.memo_category}</td>
                    <td>
                      <div className="flex items-center gap-3">
                        {/* view button always visible */}
                        <button
                          type="button"
                          className="tooltip-action-table"
                          data-tooltip="View"
                          onClick={() => handleView(item)}
                        >
                          <FaEye />
                        </button>

                        {item.memo_is_active == 1 ? (
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

      {/* modals */}
      {store.isArchive && itemEdit && (
        <ModalArchive
          mysqlApiArchive={`${apiVersion}/controllers/developers/memo/active.php?id=${itemEdit.memo_aid}`}
          msg="Are you sure you want to archive this record?"
          successMsg="Successfully archived."
          item={{ name: itemEdit.memo_category }}
          dataItem={itemEdit}
          queryKey="memo"
        />
      )}
      {store.isRestore && itemEdit && (
        <ModalRestore
          mysqlApiRestore={`${apiVersion}/controllers/developers/memo/active.php?id=${itemEdit.memo_aid}`}
          msg="Are you sure you want to restore this record?"
          successMsg="Successfully restored."
          item={{ name: itemEdit.memo_category }}
          dataItem={itemEdit}
          queryKey="memo"
        />
      )}
      {store.isDelete && itemEdit && (
        <ModalDelete
          mysqlApiDelete={`${apiVersion}/controllers/developers/memo/memo.php?id=${itemEdit.memo_aid}`}
          msg="Are you sure you want to delete this record?"
          successMsg="Successfully deleted."
          item={{ name: itemEdit.memo_category }}
          dataItem={itemEdit}
          queryKey="memo"
        />
      )}
      {isViewMemo && viewItem && (
        <ModalViewMemo
          item={viewItem}
          handleClose={() => setIsViewMemo(false)}
        />
      )}
    </>
  );
};

export default MemoList;