import { ReactElement } from 'react';

import { makeStyles } from 'tss-react/mui';

import { Paper } from '@mui/material';

import Image from '../Image/Image';
import LoadingSkeleton from '../LoadingSkeleton';

interface WallpaperPageProps {
  children: ReactElement;
  wallpaperAlt: string;
  wallpaperSource: string;
}

const useStyles = makeStyles()((theme) => ({
  contentBackground: {
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
  contentPaper: {
    alignItems: 'center',
    display: 'grid',
    flexDirection: 'column',
    justifyItems: 'center',
    minWidth: theme.spacing(30),
    padding: theme.spacing(4, 5),
    rowGap: theme.spacing(4)
  },
  wallpaper: {
    height: '100%',
    position: 'absolute',
    width: '100%'
  }
}));

const WallpaperPage = ({
  wallpaperSource,
  wallpaperAlt,
  children
}: WallpaperPageProps): JSX.Element => {
  const { classes } = useStyles();

  return (
    <div>
      <Image
        alt={wallpaperAlt}
        className={classes.wallpaper}
        fallback={<LoadingSkeleton />}
        imagePath={wallpaperSource}
      />
      <div className={classes.contentBackground}>
        <Paper className={classes.contentPaper}>{children}</Paper>
      </div>
    </div>
  );
};

export default WallpaperPage;
