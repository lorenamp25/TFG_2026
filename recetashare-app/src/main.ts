/// <reference types="@angular/localize" />

import { bootstrapApplication } from '@angular/platform-browser';
import { appConfig } from './app/app.config';
import { Index } from './app/pages/index/index';

bootstrapApplication(Index, appConfig)
  .catch((err) => console.error(err));
