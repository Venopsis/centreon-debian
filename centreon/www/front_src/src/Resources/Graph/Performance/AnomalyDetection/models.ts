import { Line, TimeValue } from '../models';

export interface FactorsData {
  currentFactor: number;
  simulatedFactor: number;
}

export interface CustomFactorsData extends FactorsData {
  isResizing: boolean;
}

export interface ExclusionPeriodThresholdData {
  isConfirmed: boolean;
  lines: Array<Line>;
  timeSeries: Array<TimeValue>;
}

export interface SelectedDateToDelete {
  end: Date | null;
  start: Date | null;
}
export interface ThresholdsAnomalyDetectionDataAtom {
  exclusionPeriodsThreshold: {
    data: Array<ExclusionPeriodThresholdData>;
    selectedDateToDelete: Array<SelectedDateToDelete>;
  };
}
