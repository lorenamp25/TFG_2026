import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecetaView } from './receta-view';

describe('RecetaView', () => {
  let component: RecetaView;
  let fixture: ComponentFixture<RecetaView>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RecetaView]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RecetaView);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
