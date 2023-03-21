import { useTranslation } from 'react-i18next';

import { Tooltip, useMediaQuery, useTheme } from '@mui/material';

import { IconButton } from '@centreon/ui';

import ActionButton from '../ActionButton';
import { labelActionNotPermitted } from '../../translatedLabels';

import useMediaQueryListing from './useMediaQueryListing';

interface Props {
  disabled: boolean;
  icon: JSX.Element;
  label: string;
  onClick: (event) => void;
  permitted?: boolean;
  secondaryIcon?: JSX.Element;
}

const ResourceActionButton = ({
  icon,
  label,
  onClick,
  disabled,
  permitted = true,
  secondaryIcon
}: Props): JSX.Element => {
  const theme = useTheme();
  const { t } = useTranslation();

  const { applyBreakPoint } = useMediaQueryListing();

  const displayCondensed =
    Boolean(useMediaQuery(theme.breakpoints.down(1100))) || applyBreakPoint;

  const title = permitted ? label : `${label} (${t(labelActionNotPermitted)})`;

  if (displayCondensed) {
    return (
      <IconButton
        ariaLabel={t(label) as string}
        data-testid={label}
        disabled={disabled}
        size="large"
        title={title}
        onClick={onClick}
      >
        {icon}
      </IconButton>
    );
  }

  return (
    <Tooltip title={permitted ? '' : labelActionNotPermitted}>
      <span>
        <ActionButton
          aria-label={t(label) as string}
          data-testid={label}
          disabled={disabled}
          endIcon={secondaryIcon}
          startIcon={icon}
          variant="contained"
          onClick={onClick}
        >
          {label}
        </ActionButton>
      </span>
    </Tooltip>
  );
};

export default ResourceActionButton;
