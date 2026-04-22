import React from "react";
import { handleEscape } from "../../../functions/functions-general";

const ModalViewMemo = ({ item, handleClose }) => {
  handleEscape(() => handleClose());

  return (
    <div className="bg-dark/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 bottom-0 left-0 z-99 flex justify-center items-center w-full md:inset-0 max-h-full animate-fadeIn">
      <div className="p-1 w-full max-w-2xl animate-slideUp">
        <div className="bg-white rounded-lg shadow-xl relative">

          {/* header info */}
          <div className="p-6 pb-2 border-b border-gray-100">
            <table className="w-auto text-sm">
              <tbody>
                <tr>
                  <td className="pr-6 py-1 font-semibold text-gray-600">To:</td>
                  <td className="py-1">{item.memo_to}</td>
                </tr>
                <tr>
                  <td className="pr-6 py-1 font-semibold text-gray-600">From:</td>
                  <td className="py-1">{item.memo_from}</td>
                </tr>
                <tr>
                  <td className="pr-6 py-1 font-semibold text-gray-600">Date:</td>
                  <td className="py-1">{item.memo_date}</td>
                </tr>
                <tr>
                  <td className="pr-6 py-1 font-semibold text-gray-600">Category:</td>
                  <td className="py-1">{item.memo_category}</td>
                </tr>
              </tbody>
            </table>
          </div>

          {/* memo body */}
          <div className="p-6 min-h-[300px] max-h-[60vh] overflow-y-auto">
            <p className="text-sm whitespace-pre-wrap leading-relaxed">
              {item.memo_text}
            </p>
          </div>

          {/* footer */}
          <div className="p-4 border-t border-gray-200 flex justify-end">
            <button
              type="button"
              className="px-5 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50"
              onClick={handleClose}
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ModalViewMemo;