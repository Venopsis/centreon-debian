import { lazy, Suspense } from 'react';

import { Formik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useAtomValue } from 'jotai/utils';
import { isNil } from 'ramda';
import { makeStyles } from 'tss-react/mui';

import { Paper, Typography } from '@mui/material';

import { CentreonLogo, LoadingSkeleton } from '@centreon/ui';

import { areUserParametersLoadedAtom } from '../Main/useUser';
import { MainLoaderWithoutTranslation } from '../Main/MainLoader';
import Wallpaper from '../components/Wallpaper';
import { platformVersionsAtom } from '../Main/atoms/platformVersionsAtom';

import useValidationSchema from './validationSchema';
import { LoginFormValues } from './models';
import useLogin from './useLogin';
import { labelLogin } from './translatedLabels';

const ExternalProviders = lazy(() => import('./ExternalProviders'));

const Copyright = lazy(() => import('./Copyright'));

const LoginForm = lazy(() => import('./Form'));

const useStyles = makeStyles()((theme) => ({
  copyrightAndVersion: {
    alignItems: 'center',
    display: 'flex',
    flexDirection: 'column',
    rowGap: theme.spacing(0.5)
  },
  copyrightSkeleton: {
    width: theme.spacing(16)
  },
  loginBackground: {
    alignItems: 'center',
    backgroundColor: 'transparent',
    display: 'flex',
    filter: 'brightness(1)',
    flexDirection: 'column',
    height: '100vh',
    justifyContent: 'center',
    rowGap: theme.spacing(2),
    width: '100%'
  },
  loginPaper: {
    alignItems: 'center',
    display: 'grid',
    flexDirection: 'column',
    justifyItems: 'center',
    minWidth: theme.spacing(30),
    padding: theme.spacing(4, 5),
    rowGap: theme.spacing(4)
  }
}));

const initialValues: LoginFormValues = {
  alias: '',
  password: ''
};

const LoginPage = (): JSX.Element => {
  const { classes } = useStyles();
  const { t } = useTranslation();
  const validationSchema = useValidationSchema();

  const { submitLoginForm, providersConfiguration } = useLogin();

  const areUserParametersLoaded = useAtomValue(areUserParametersLoadedAtom);
  const platformVersions = useAtomValue(platformVersionsAtom);

  if (areUserParametersLoaded || isNil(areUserParametersLoaded)) {
    return <MainLoaderWithoutTranslation />;
  }

  const hasProvidersConfiguration = !isNil(providersConfiguration);

  return (
    <div>
      <Wallpaper />
      <div className={classes.loginBackground}>
        <Paper className={classes.loginPaper}>
          <Suspense
            fallback={
              <LoadingSkeleton height={60} variant="text" width={250} />
            }
          >
            <CentreonLogo />
          </Suspense>
          <Suspense
            fallback={
              <LoadingSkeleton height={30} variant="text" width={115} />
            }
          >
            <Typography variant="h5">{t(labelLogin)}</Typography>
          </Suspense>
          <div>
            <Formik<LoginFormValues>
              validateOnMount
              initialValues={initialValues}
              validationSchema={validationSchema}
              onSubmit={submitLoginForm}
            >
              <Suspense
                fallback={
                  <LoadingSkeleton height={45} variant="text" width={250} />
                }
              >
                <LoginForm />
              </Suspense>
            </Formik>
            {hasProvidersConfiguration && (
              <Suspense
                fallback={
                  <LoadingSkeleton height={45} variant="text" width={250} />
                }
              >
                <ExternalProviders
                  providersConfiguration={providersConfiguration}
                />
              </Suspense>
            )}
          </div>
          <div className={classes.copyrightAndVersion}>
            <Suspense
              fallback={
                <LoadingSkeleton
                  className={classes.copyrightSkeleton}
                  variant="text"
                />
              }
            >
              <Copyright />
            </Suspense>
            {isNil(platformVersions) ? (
              <LoadingSkeleton variant="text" width="40%" />
            ) : (
              <Typography variant="body2">
                v. {platformVersions?.web.version}
              </Typography>
            )}
          </div>
        </Paper>
      </div>
    </div>
  );
};

export default LoginPage;
