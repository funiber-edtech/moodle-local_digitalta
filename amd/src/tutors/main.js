import Notification from "core/notification";
import { setEventListeners } from "local_digitalta/tutors/listeners";
import { tutoringRequestsAdd, tutoringRequestsRemove } from "local_digitalta/repositories/tutoring_repository";

export const sendTutorRequest = (id) => {
  const args = { experienceid: id, tutorid: null, experienceRequest: 1 };
  tutoringRequestsAdd(args)
    .then(() => {
      window.location.reload();
      return;
    })
    .catch((error) => {
      Notification.exception(error);
    });
};

export const cancelTutorRequest = (id) => {
  const args = { experienceid: id, tutorid: null };
  tutoringRequestsRemove(args)
    .then(() => {
      window.location.reload();
      return;
    })
    .catch((error) => {
      Notification.exception(error);
    });
};

export const init = () => {
  setEventListeners();
};
