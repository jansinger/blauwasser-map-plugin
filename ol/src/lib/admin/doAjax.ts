export const doAjax = async (nonce: string, data: {}) => {
  /* @ts-ignore */
  const postId = document.getElementById("post_ID").value;
  const response = await fetch(ajaxurl, {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "Cache-Control": "no-cache",
    },
    body: new URLSearchParams({ _ajax_nonce: nonce, postId, ...data }),
  });
  const result = await response.json();
  return { response, result };
};
