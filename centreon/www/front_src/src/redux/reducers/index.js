import { combineReducers } from "redux";
import { routerReducer } from "react-router-redux";
import { reducer as formReducer } from "redux-form";

import pollerWizardReducer from './pollerWizardReducer';

export default combineReducers({
  form: formReducer,
  poller: pollerWizardReducer
});
